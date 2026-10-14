<?php

namespace App\Enums;

enum UserRole: string
{
    case DocenteTitular    = 'docente_titular';
    case DocenteEspecial   = 'docente_especial';
    case Psicopedagogia    = 'psicopedagogia';
    case CoordinacionAcademica = 'coordinacion_academica';
    case Direccion         = 'direccion';
    case AdminTecnico      = 'admin_tecnico';

    public function label(): string
    {
        return match($this) {
            self::DocenteTitular       => 'Docente Titular',
            self::DocenteEspecial      => 'Docente de Materia Especial',
            self::Psicopedagogia       => 'Departamento Psicopedagógico',
            self::CoordinacionAcademica => 'Coordinación Académica',
            self::Direccion            => 'Dirección Escolar',
            self::AdminTecnico         => 'Administrador Técnico',
        };
    }

    public function canAccessPsychContent(): bool
    {
        return match($this) {
            self::AdminTecnico => false,
            default            => true,
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
