<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Activo      = 'activo';
    case EnSeguimiento = 'en_seguimiento';
    case Cerrado     = 'cerrado';

    public function label(): string
    {
        return match($this) {
            self::Activo        => 'Activo',
            self::EnSeguimiento => 'En seguimiento',
            self::Cerrado       => 'Cerrado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Activo        => 'green',
            self::EnSeguimiento => 'yellow',
            self::Cerrado       => 'gray',
        };
    }
}
