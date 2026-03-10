<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    use Exportable;

    protected array $ids;

    public function __construct(array $ids = [])
    {
        $this->ids = $ids;
    }

    public function query(): Builder
    {
        $query = User::query()->with('tenant')->orderBy('name');

        if (!empty($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nombre',
            'Email',
            'DNI',
            'Institución',
            'Cargo',
            'Rol',
            'Estado',
            'Último Acceso',
            'Fecha de Registro',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->dni ?? '—',
            $user->tenant?->nombre ?? 'UGEL Huacaybamba',
            $user->cargo ?? '—',
            $user->role,
            $user->is_active ? 'Activo' : 'Inactivo',
            $user->last_login_at?->format('d/m/Y H:i') ?? 'Nunca',
            $user->created_at->format('d/m/Y'),
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
