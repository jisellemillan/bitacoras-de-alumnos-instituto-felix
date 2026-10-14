<?php

namespace App\Enums;

enum AgreementStatus: string
{
    case Pendiente  = 'pendiente';
    case EnProceso  = 'en_proceso';
    case Cumplido   = 'cumplido';
    case Cancelado  = 'cancelado';

    public function label(): string
    {
        return match($this) {
            self::Pendiente => 'Pendiente',
            self::EnProceso => 'En proceso',
            self::Cumplido  => 'Cumplido',
            self::Cancelado => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Pendiente => 'yellow',
            self::EnProceso => 'blue',
            self::Cumplido  => 'green',
            self::Cancelado => 'gray',
        };
    }
}
