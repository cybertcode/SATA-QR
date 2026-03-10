<?php

namespace App\Exports;

use App\Models\Estudiante;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    public function __construct(
        protected ?string $tenantId = null,
        protected ?string $nivel = null,
        protected ?string $grado = null,
        protected ?string $estado = null,
        protected ?string $search = null,
    ) {
    }

    public function query(): Builder
    {
        $query = Estudiante::query()
            ->with(['tenant', 'matriculaActual.seccion.nivelEducativo'])
            ->orderBy('apellido_paterno')
            ->orderBy('apellido_materno');

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        }

        if ($this->nivel) {
            $query->whereHas('matriculaActual.seccion.nivelEducativo', fn($q) => $q->where('nivel', $this->nivel));
        }

        if ($this->grado) {
            $query->whereHas('matriculaActual.seccion', fn($q) => $q->where('grado', $this->grado));
        }

        if ($this->estado) {
            if ($this->estado === 'sin_matricula') {
                $query->doesntHave('matriculaActual');
            } else {
                $query->whereHas('matriculaActual', fn($q) => $q->where('estado', $this->estado));
            }
        }

        if ($this->search) {
            $s = $this->search;
            $query->where(function ($q) use ($s) {
                $q->where('dni', 'like', "%{$s}%")
                    ->orWhere('nombres', 'like', "%{$s}%")
                    ->orWhere('apellido_paterno', 'like', "%{$s}%")
                    ->orWhere('apellido_materno', 'like', "%{$s}%");
            });
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'DNI',
            'Apellido Paterno',
            'Apellido Materno',
            'Nombres',
            'Género',
            'Institución Educativa',
            'Nivel',
            'Grado',
            'Sección',
            'Estado Matrícula',
        ];
    }

    public function map($student): array
    {
        $matricula = $student->matriculaActual;

        return [
            $student->dni,
            $student->apellido_paterno,
            $student->apellido_materno,
            $student->nombres,
            $student->genero === 'M' ? 'Masculino' : 'Femenino',
            $student->tenant?->nombre ?? '—',
            $matricula?->seccion?->nivelEducativo?->nivel ?? '—',
            $matricula?->seccion?->grado ?? '—',
            $matricula?->seccion?->letra ?? '—',
            $matricula?->estado ?? 'Sin matrícula',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
