<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SiagieStudentImport implements ToArray, WithHeadingRow, WithValidation
{
    private array $rows = [];

    public function array(array $rows): void
    {
        $this->rows = $rows;
    }

    public function rules(): array
    {
        return [
            '*.dni_estudiante' => 'required|string|size:8',
            '*.nombres' => 'required|string|max:100',
            '*.apellido_paterno' => 'required|string|max:100',
            '*.apellido_materno' => 'required|string|max:100',
            '*.sexo' => 'required|string|in:M,F,H',
            '*.grado' => 'required|string',
            '*.seccion' => 'required|string|max:1',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '*.dni_estudiante.required' => 'El DNI es obligatorio en la fila :attribute.',
            '*.dni_estudiante.size' => 'El DNI debe tener 8 dígitos en la fila :attribute.',
            '*.nombres.required' => 'Los nombres son obligatorios en la fila :attribute.',
            '*.apellido_paterno.required' => 'El apellido paterno es obligatorio en la fila :attribute.',
            '*.apellido_materno.required' => 'El apellido materno es obligatorio en la fila :attribute.',
            '*.sexo.required' => 'El sexo es obligatorio en la fila :attribute.',
            '*.sexo.in' => 'El sexo debe ser M, F o H en la fila :attribute.',
            '*.grado.required' => 'El grado es obligatorio en la fila :attribute.',
            '*.seccion.required' => 'La sección es obligatoria en la fila :attribute.',
        ];
    }

    /**
     * Transforma las filas al formato esperado por StudentImportService.
     */
    public function getRows(): array
    {
        return array_map(function ($row) {
            $genero = strtoupper(trim($row['sexo'] ?? ''));
            // SIAGIE usa "H" para hombres, normalizamos a "M"
            if ($genero === 'H') {
                $genero = 'M';
            }

            return [
                'dni' => trim($row['dni_estudiante']),
                'nombres' => mb_strtoupper(trim($row['nombres'])),
                'paterno' => mb_strtoupper(trim($row['apellido_paterno'])),
                'materno' => mb_strtoupper(trim($row['apellido_materno'])),
                'genero' => $genero,
                'grado' => trim($row['grado']),
                'seccion' => mb_strtoupper(trim($row['seccion'])),
            ];
        }, $this->rows);
    }
}
