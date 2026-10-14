<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false));
    }
}; ?>

<div>
    <!-- Estado de sesión -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <!-- Correo Electrónico -->
        <div class="form-group">
            <label for="email" class="form-label required font-bold text-xs text-gray-700">Correo Electrónico Institucional</label>
            <input wire:model="form.email"
                   id="email"
                   type="email"
                   name="email"
                   required
                   autofocus
                   autocomplete="username"
                   placeholder="ejemplo@felix.edu.mx"
                   class="form-input text-sm @error('form.email') error @enderror">
            @error('form.email')
                <span class="form-error mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Contraseña -->
        <div class="form-group">
            <label for="password" class="form-label required font-bold text-xs text-gray-700">Contraseña</label>
            <input wire:model="form.password"
                   id="password"
                   type="password"
                   name="password"
                   required
                   autocomplete="current-password"
                   placeholder="••••••••"
                   class="form-input text-sm @error('form.password') error @enderror">
            @error('form.password')
                <span class="form-error mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Recordarme -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember" class="flex items-center gap-2 cursor-pointer select-none">
                <input wire:model="form.remember"
                       id="remember"
                       type="checkbox"
                       class="form-check-input">
                <span class="text-xs text-gray-600">Recordar sesión</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-xs text-green-800 hover:text-green-950 hover:underline font-medium"
                   href="{{ route('password.request') }}"
                   wire:navigate>
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <!-- Botón de Inicio de Sesión con estado de carga -->
        <div class="pt-2">
            <button type="submit"
                    class="btn btn-primary w-full py-2.5 font-bold text-sm shadow-md flex items-center justify-center gap-2"
                    wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="login">Iniciar Sesión</span>
                <span wire:loading wire:target="login" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Verificando credenciales...</span>
                </span>
            </button>
        </div>
    </form>

    <div class="auth-footer mt-6 text-center text-xs text-muted border-t pt-4">
        <span>Sección Primaria · Mexicali, B.C.</span>
    </div>
</div>
