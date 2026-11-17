<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:send-interview-reminders')]
#[Description('Command description')]
class SendInterviewReminders extends Command
{
    /**
     * Execute the console command.
     */
   public function handle()
    {
        $inTwoDays = now()->addDays(2)->format('Y-m-d');
        
        $this->info("1. Buscando citas exactamente para la fecha: {$inTwoDays}");
        
        // Usamos tu modelo correcto
        $citasEnDosDias = \App\Models\Appointment::whereDate('starts_at', $inTwoDays)->get();
        
        $this->info("2. Encontré " . $citasEnDosDias->count() . " cita(s) programada(s) para ese día.");

        foreach ($citasEnDosDias as $cita) {
            $this->info("3. Revisando cita ID: {$cita->id}, creada por el usuario ID: {$cita->created_by}");
            
            $user = \App\Models\User::find($cita->created_by);
            
            if ($user) {
                $user->notify(new \App\Notifications\InterviewReminder($cita, 'en 2 días'));
                $this->info("4. ¡Éxito! Notificación enviada al usuario: " . ($user->email ?? $user->name));
            } else {
                $this->error("ERROR: No encontré a ningún usuario con el ID {$cita->created_by} en la base de datos.");
            }
        }

        $this->info('--- Proceso terminado ---');
    }
}
