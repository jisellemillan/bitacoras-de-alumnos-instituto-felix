<?php

namespace App\Enums;

enum InterviewModality: string
{
    case Presencial    = 'presencial';
    case EnLinea       = 'en_linea';
    case ViaTetefonica = 'via_telefonica';

    public function label(): string
    {
        return match($this) {
            self::Presencial    => 'Presencial',
            self::EnLinea       => 'En línea',
            self::ViaTetefonica => 'Vía telefónica',
        };
    }
}
