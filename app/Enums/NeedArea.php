<?php

namespace App\Enums;

enum NeedArea: string
{
    case AtencionFuncionesEjecutivas = 'atencion_funciones_ejecutivas';
    case LenguajeComunicacion        = 'lenguaje_comunicacion';
    case Aprendizaje                 = 'aprendizaje';
    case NeurodesarrolloTEA          = 'neurodesarrollo_tea';
    case ConductualEmocional         = 'conductual_emocional';
    case CondicionesMedicasNeurologicas = 'condiciones_medicas_neurologicas';

    public function label(): string
    {
        return match($this) {
            self::AtencionFuncionesEjecutivas   => 'Atención y funciones ejecutivas',
            self::LenguajeComunicacion          => 'Lenguaje y comunicación',
            self::Aprendizaje                   => 'Aprendizaje',
            self::NeurodesarrolloTEA            => 'Neurodesarrollo / TEA',
            self::ConductualEmocional           => 'Conductual / emocional',
            self::CondicionesMedicasNeurologicas => 'Condiciones médicas / neurológicas',
        };
    }
}
