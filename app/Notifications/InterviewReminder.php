<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class InterviewReminder extends Notification
{
    use Queueable;

    public $interview;
    public $timeMessage;

    public function __construct($interview, $timeMessage)
    {
        $this->interview = $interview;
        $this->timeMessage = $timeMessage; // Ej. "en 15 minutos" o "en 2 días"
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Solo lo guardaremos en la base de datos (campana)
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Recordatorio de Cita', // Cambiado de Entrevista a Cita
            'message' => "Tienes una cita programada {$this->timeMessage} con el alumno/tutor.",
            'interview_id' => $this->interview->id, 
        ];
    }
}