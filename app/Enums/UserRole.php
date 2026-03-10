<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdmin = 'SuperAdmin';
    case Administrador = 'Administrador';
    case Director = 'Director';
    case Docente = 'Docente';
    case Auxiliar = 'Auxiliar';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Administrador',
            self::Administrador => 'Administrador',
            self::Director => 'Director I.E.',
            self::Docente => 'Docente PIP',
            self::Auxiliar => 'Auxiliar',
        };
    }

    /**
     * Jerarquía numérica (mayor = más privilegios).
     */
    public function level(): int
    {
        return match ($this) {
            self::SuperAdmin => 100,
            self::Administrador => 80,
            self::Director => 60,
            self::Docente => 40,
            self::Auxiliar => 20,
        };
    }

    /**
     * Roles que NO requieren tenant (nivel UGEL).
     */
    public function requiresTenant(): bool
    {
        return !in_array($this, [self::SuperAdmin, self::Administrador]);
    }

    /**
     * Todos los valores como array para validación/filtros.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Array asociativo [value => value] para selects/filtros de Rappasoft.
     */
    public static function options(): array
    {
        return array_combine(self::values(), self::values());
    }

    /**
     * Verifica si este rol puede gestionar al rol objetivo.
     */
    public function canManage(self $target): bool
    {
        return $this->level() > $target->level();
    }
}
