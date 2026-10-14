<?php

namespace App\Enums;

enum ObservationRequiredAction: string
{
    case EnviarPrefectura  = 'enviar_prefectura';
    case EntrevistaConPapas = 'entrevista_con_papas';
    case Canalizacion      = 'canalizacion';

    public function label(): string
    {
        return match($this) {
            self::EnviarPrefectura   => 'Enviar a prefectura',
            self::EntrevistaConPapas => 'Entrevista con papás',
            self::Canalizacion       => 'Canalización',
        };
    }
}
