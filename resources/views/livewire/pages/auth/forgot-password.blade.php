<?php

use App\Models\PasswordRequest;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';
    public bool $enviado = false;

    public function enviarSolicitud(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email'    => 'Ingresa un correo electrónico válido.',
        ]);

        // Verificar si el correo pertenece a un usuario registrado
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            // No revelamos si el correo existe o no (seguridad)
            $this->enviado = true;
            return;
        }

        // Verificar si ya existe una solicitud pendiente para evitar duplicados
        $solicitudExistente = PasswordRequest::where('email', $this->email)
            ->where('estado', 'pendiente')
            ->exists();

        if (!$solicitudExistente) {
            PasswordRequest::create([
                'email'   => $this->email,
                'user_id' => $user->id,
                'estado'  => 'pendiente',
            ]);
        }

        $this->enviado = true;
    }
}; ?>

<div>
    @if($enviado)
        {{-- Mensaje de confirmación --}}
        <div style="text-align:center; padding: 1rem 0;">
            <div style="width:56px; height:56px; border-radius:50%; background:#dcfce7; display:flex; align-items:center; justify-content:center; margin: 0 auto 1rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h2 style="font-size:1.1rem; font-weight:700; color:#14532d; margin-bottom:0.5rem;">Solicitud enviada</h2>
            <p style="font-size:0.875rem; color:#6b7280; line-height:1.6; margin-bottom:1.5rem;">
                Tu solicitud de recuperación de contraseña ha sido registrada.<br>
                La administradora del sistema te contactará para proporcionarte tu nueva contraseña.
            </p>
            <a href="{{ route('login') }}"
               style="display:inline-block; padding: 0.5rem 1.25rem; background:#1a1a2e; color:white; border-radius:0.5rem; font-size:0.875rem; text-decoration:none; font-weight:600;">
                Volver al inicio de sesión
            </a>
        </div>
    @else
        <div class="mb-5 text-sm text-gray-600" style="line-height:1.6;">
            Ingresa el correo electrónico con el que estás registrado en el sistema. 
            La administradora recibirá tu solicitud y te proporcionará una nueva contraseña.
        </div>

        <form wire:submit="enviarSolicitud">
            <div>
                <x-input-label for="email" value="Correo electrónico" />
                <x-text-input wire:model="email"
                              id="email"
                              class="block mt-1 w-full"
                              type="email"
                              name="email"
                              required
                              autofocus
                              placeholder="tucorreo@ejemplo.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between mt-5 gap-3">
                <a href="{{ route('login') }}"
                   style="font-size:0.875rem; color:#6b7280; text-decoration:none;">
                    ← Volver
                </a>
                <x-primary-button wire:loading.attr="disabled">
                    <span wire:loading.remove>Enviar solicitud</span>
                    <span wire:loading>Enviando...</span>
                </x-primary-button>
            </div>
        </form>
    @endif
</div>
