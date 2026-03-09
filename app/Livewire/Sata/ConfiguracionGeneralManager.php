<?php

namespace App\Livewire\Sata;

use App\Models\ConfiguracionGeneral;
use App\Services\ConfiguracionGeneralService;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ConfiguracionGeneralManager extends Component
{
    public string $activeGroup = 'general';
    public array $valores = [];
    public array $stats = [];

    public function mount(ConfiguracionGeneralService $service): void
    {
        Gate::authorize('viewAny', ConfiguracionGeneral::class);
        $this->refreshStats($service);
        $this->loadGroup();
    }

    public function switchGroup(string $grupo): void
    {
        $this->activeGroup = $grupo;
        $this->loadGroup();
        $this->resetValidation();
    }

    private function loadGroup(): void
    {
        $configs = ConfiguracionGeneral::grupo($this->activeGroup)
            ->orderBy('orden')
            ->get();

        $this->valores = [];
        foreach ($configs as $config) {
            $this->valores[$config->id] = $config->tipo === 'boolean'
                ? filter_var($config->valor, FILTER_VALIDATE_BOOLEAN)
                : $config->valor;
        }
    }

    public function save(ConfiguracionGeneralService $service): void
    {
        Gate::authorize('update', ConfiguracionGeneral::class);

        $configs = ConfiguracionGeneral::grupo($this->activeGroup)->get();

        // Validar según tipo
        $rules = [];
        $messages = [];
        foreach ($configs as $config) {
            $rule = match ($config->tipo) {
                'integer' => ['required', 'integer', 'min:0'],
                'boolean' => ['required', 'boolean'],
                default => ['required', 'string', 'max:500'],
            };
            $rules["valores.{$config->id}"] = $rule;
            $messages["valores.{$config->id}.required"] = "El campo \"{$config->etiqueta}\" es obligatorio.";
            $messages["valores.{$config->id}.integer"] = "El campo \"{$config->etiqueta}\" debe ser un número entero.";
            $messages["valores.{$config->id}.min"] = "El campo \"{$config->etiqueta}\" debe ser mayor o igual a 0.";
        }

        $this->validate($rules, $messages);

        // Mapear IDs a claves para el service
        $valoresPorClave = [];
        foreach ($configs as $config) {
            if (array_key_exists($config->id, $this->valores)) {
                $valoresPorClave[$config->clave] = $this->valores[$config->id];
            }
        }

        $updated = $service->updateBatch($valoresPorClave);
        $this->refreshStats($service);

        $this->dispatch(
            'swal',
            icon: $updated > 0 ? 'success' : 'info',
            title: $updated > 0
            ? "{$updated} configuración(es) actualizada(s)."
            : 'Sin cambios realizados.'
        );
    }

    public function resetGroup(): void
    {
        $this->loadGroup();
        $this->resetValidation();
        $this->dispatch('swal', icon: 'info', title: 'Valores restaurados a los guardados.');
    }

    private function refreshStats(ConfiguracionGeneralService $service): void
    {
        $this->stats = $service->getStats();
    }

    public function render()
    {
        $configs = ConfiguracionGeneral::grupo($this->activeGroup)
            ->orderBy('orden')
            ->get();

        return view('livewire.sata.configuracion-general-manager', [
            'configs' => $configs,
            'gruposMeta' => ConfiguracionGeneralService::gruposMeta(),
        ]);
    }
}
