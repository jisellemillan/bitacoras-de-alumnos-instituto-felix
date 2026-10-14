<?php

namespace App\Enums;

enum IntakeRoute: string
{
    case A = 'A';
    case B = 'B';
    case C = 'C';

    public function label(): string
    {
        return match($this) {
            self::A => 'Ruta A — Detección por docente',
            self::B => 'Ruta B — Solicitud de familia',
            self::C => 'Ruta C — Diagnóstico previo externo',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::A => 'Observación del docente → formato → entrega a psicopedagogía → registro → comunicación → seguimiento',
            self::B => 'Los padres solicitan apoyo → psicopedagogía entrevista y observa → registro → comunica a docentes → seguimiento',
            self::C => 'Recepción de diagnóstico externo → comunicación con docentes → registro → seguimiento anual',
        };
    }
}
