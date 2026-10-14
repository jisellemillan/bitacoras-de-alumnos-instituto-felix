<?php

namespace App\Enums;

enum SignatureRole: string
{
    case MadreTutor              = 'madre_tutor';
    case PadreTutor              = 'padre_tutor';
    case MaestroEspanol          = 'maestro_espanol';
    case MaestroIngles           = 'maestro_ingles';
    case Psicopedagogico         = 'psicopedagogico';
    case Especialista            = 'especialista';
    case DireccionAcademica      = 'direccion_academica';
    case CoordinacionEstudios    = 'coordinacion_estudios';

    public function label(): string
    {
        return match($this) {
            self::MadreTutor           => 'Nombre y firma madre o tutor',
            self::PadreTutor           => 'Nombre y firma padre o tutor',
            self::MaestroEspanol       => 'Nombre y firma maestro de español',
            self::MaestroIngles        => 'Nombre y firma maestro de inglés',
            self::Psicopedagogico      => 'Nombre y firma Psicopedagógico',
            self::Especialista         => 'Nombre y firma del Especialista',
            self::DireccionAcademica   => 'Dirección Académica Primaria',
            self::CoordinacionEstudios => 'Coordinación de estudios español y/o inglés',
        };
    }
}
