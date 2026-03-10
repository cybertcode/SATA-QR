<?php

namespace App\Livewire\Sata;

use App\Models\CalendarioFeriado;
use App\Models\ConfiguracionAsistencia;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Livewire\Component;

class InstitutionSettingsManager extends Component
{
    public string $activeTab = 'horarios';

    // ─── Horarios ───
    public string $hora_entrada_regular = '07:45';
    public int $minutos_tolerancia = 15;
    public int $dias_inasistencia_riesgo = 3;

    // ─── Identidad Visual ───
    public string $primary_color = '#4f46e5';
    public string $lema = '';

    // ─── Feriados ───
    public string $feriado_fecha = '';
    public string $feriado_descripcion = '';
    public ?int $editingFeriadoId = null;

    // ─── Tenant ───
    public ?string $tenantId = null;
    public ?string $tenantNombre = null;
    public bool $isSuperAdmin = false;

    public function mount(): void
    {
        $user = auth()->user();
        $this->isSuperAdmin = $user->isSuperAdmin();

        if ($this->isSuperAdmin) {
            // SuperAdmin: seleccionar la primera IE disponible
            $first = Tenant::orderBy('nombre')->first();
            if ($first) {
                $this->tenantId = $first->id;
                $this->tenantNombre = $first->nombre;
            }
        } else {
            $this->tenantId = $user->tenant_id;
            if ($this->tenantId) {
                $this->tenantNombre = Tenant::find($this->tenantId)?->nombre;
            }
        }

        if ($this->tenantId) {
            $this->loadHorarios();
            $this->loadIdentidad(Tenant::find($this->tenantId));
        }
    }

    public function selectTenant(string $id): void
    {
        $tenant = Tenant::findOrFail($id);
        $this->tenantId = $tenant->id;
        $this->tenantNombre = $tenant->nombre;

        // Reset state
        $this->activeTab = 'horarios';
        $this->hora_entrada_regular = '07:45';
        $this->minutos_tolerancia = 15;
        $this->dias_inasistencia_riesgo = 3;
        $this->primary_color = '#4f46e5';
        $this->lema = '';
        $this->resetFeriadoForm();
        $this->resetValidation();

        // Reload data
        $this->loadHorarios();
        $this->loadIdentidad($tenant);
    }

    // ══════════════════════════════════════════════════════════
    //  TABS
    // ══════════════════════════════════════════════════════════

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetValidation();
        $this->resetFeriadoForm();
    }

    // ══════════════════════════════════════════════════════════
    //  HORARIOS
    // ══════════════════════════════════════════════════════════

    private function loadHorarios(): void
    {
        $config = ConfiguracionAsistencia::where('tenant_id', $this->tenantId)->first();

        if ($config) {
            $this->hora_entrada_regular = substr($config->hora_entrada_regular, 0, 5);
            $this->minutos_tolerancia = $config->minutos_tolerancia;
            $this->dias_inasistencia_riesgo = $config->dias_inasistencia_riesgo ?? 3;
        }
    }

    public function saveHorarios(): void
    {
        $this->validate([
            'hora_entrada_regular' => ['required', 'date_format:H:i'],
            'minutos_tolerancia' => ['required', 'integer', 'min:0', 'max:60'],
            'dias_inasistencia_riesgo' => ['required', 'integer', 'min:1', 'max:30'],
        ], [
            'hora_entrada_regular.required' => 'La hora de ingreso es obligatoria.',
            'hora_entrada_regular.date_format' => 'El formato de hora debe ser HH:MM.',
            'minutos_tolerancia.required' => 'Los minutos de tolerancia son obligatorios.',
            'minutos_tolerancia.min' => 'Los minutos no pueden ser negativos.',
            'minutos_tolerancia.max' => 'Los minutos no pueden superar 60.',
            'dias_inasistencia_riesgo.required' => 'Los días de inasistencia son obligatorios.',
            'dias_inasistencia_riesgo.min' => 'Debe ser al menos 1 día.',
            'dias_inasistencia_riesgo.max' => 'No puede superar 30 días.',
        ]);

        ConfiguracionAsistencia::updateOrCreate(
            ['tenant_id' => $this->tenantId],
            [
                'hora_entrada_regular' => $this->hora_entrada_regular . ':00',
                'minutos_tolerancia' => $this->minutos_tolerancia,
                'dias_inasistencia_riesgo' => $this->dias_inasistencia_riesgo,
            ]
        );

        $this->dispatch('swal', icon: 'success', title: 'Configuración de horarios guardada correctamente.');
    }

    // ══════════════════════════════════════════════════════════
    //  IDENTIDAD VISUAL
    // ══════════════════════════════════════════════════════════

    private function loadIdentidad(?Tenant $tenant): void
    {
        if (!$tenant) {
            return;
        }

        $config = $tenant->config ?? [];
        $this->primary_color = $config['primary_color'] ?? '#4f46e5';
        $this->lema = $config['lema'] ?? '';
    }

    public function saveIdentidad(): void
    {
        $this->validate([
            'primary_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'lema' => ['nullable', 'string', 'max:200'],
        ], [
            'primary_color.required' => 'El color primario es obligatorio.',
            'primary_color.regex' => 'El color debe ser un código hexadecimal válido (#RRGGBB).',
            'lema.max' => 'El lema no puede superar los 200 caracteres.',
        ]);

        $tenant = Tenant::find($this->tenantId);
        $config = $tenant->config ?? [];
        $config['primary_color'] = $this->primary_color;
        $config['lema'] = $this->lema;
        $tenant->config = $config;
        $tenant->save();

        $this->dispatch('swal', icon: 'success', title: 'Identidad visual actualizada correctamente.');
    }

    // ══════════════════════════════════════════════════════════
    //  FERIADOS
    // ══════════════════════════════════════════════════════════

    public function saveFeriado(): void
    {
        $this->validate([
            'feriado_fecha' => ['required', 'date'],
            'feriado_descripcion' => ['required', 'string', 'max:150'],
        ], [
            'feriado_fecha.required' => 'La fecha es obligatoria.',
            'feriado_fecha.date' => 'Debe ser una fecha válida.',
            'feriado_descripcion.required' => 'La descripción es obligatoria.',
            'feriado_descripcion.max' => 'La descripción no puede superar 150 caracteres.',
        ]);

        // Verificar duplicado (misma fecha + tenant)
        $exists = CalendarioFeriado::where('tenant_id', $this->tenantId)
            ->whereDate('fecha', $this->feriado_fecha)
            ->when($this->editingFeriadoId, fn($q) => $q->where('id', '!=', $this->editingFeriadoId))
            ->exists();

        if ($exists) {
            $this->addError('feriado_fecha', 'Ya existe un feriado registrado para esta fecha.');
            return;
        }

        if ($this->editingFeriadoId) {
            $feriado = CalendarioFeriado::where('id', $this->editingFeriadoId)
                ->where('tenant_id', $this->tenantId)
                ->firstOrFail();
            $feriado->update([
                'fecha' => $this->feriado_fecha,
                'descripcion' => $this->feriado_descripcion,
            ]);
            $msg = 'Feriado actualizado correctamente.';
        } else {
            CalendarioFeriado::create([
                'tenant_id' => $this->tenantId,
                'fecha' => $this->feriado_fecha,
                'descripcion' => $this->feriado_descripcion,
            ]);
            $msg = 'Feriado registrado correctamente.';
        }

        $this->resetFeriadoForm();
        $this->dispatch('swal', icon: 'success', title: $msg);
    }

    public function editFeriado(int $id): void
    {
        $feriado = CalendarioFeriado::where('id', $id)
            ->where('tenant_id', $this->tenantId)
            ->firstOrFail();

        $this->editingFeriadoId = $id;
        $this->feriado_fecha = $feriado->fecha->format('Y-m-d');
        $this->feriado_descripcion = $feriado->descripcion;
    }

    public function deleteFeriado(int $id): void
    {
        CalendarioFeriado::where('id', $id)
            ->where('tenant_id', $this->tenantId)
            ->delete();

        $this->dispatch('swal', icon: 'success', title: 'Feriado eliminado.');
    }

    public function cancelEditFeriado(): void
    {
        $this->resetFeriadoForm();
    }

    private function resetFeriadoForm(): void
    {
        $this->editingFeriadoId = null;
        $this->feriado_fecha = '';
        $this->feriado_descripcion = '';
        $this->resetValidation(['feriado_fecha', 'feriado_descripcion']);
    }

    // ══════════════════════════════════════════════════════════
    //  CIERRE DE ASISTENCIA
    // ══════════════════════════════════════════════════════════

    public function closeDay(): void
    {
        Artisan::call('sata:close-day', ['date' => now()->toDateString()]);

        $this->dispatch('swal', icon: 'success', title: 'Cierre de asistencia ejecutado correctamente.');
    }

    // ══════════════════════════════════════════════════════════
    //  RENDER
    // ══════════════════════════════════════════════════════════

    public function render()
    {
        $feriados = collect();
        if ($this->activeTab === 'feriados' && $this->tenantId) {
            $feriados = CalendarioFeriado::where('tenant_id', $this->tenantId)
                ->orderBy('fecha', 'desc')
                ->get();
        }

        $tenants = $this->isSuperAdmin
            ? Tenant::orderBy('nombre')->get(['id', 'nombre'])
            : collect();

        return view('livewire.sata.institution-settings-manager', [
            'feriados' => $feriados,
            'tenants' => $tenants,
        ]);
    }
}
