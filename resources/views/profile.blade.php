<x-app-layout title="Mi Perfil">
    <div class="space-y-6 max-w-4xl mx-auto">
        {{-- Tarjeta de bienvenida / encabezado del usuario --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-600 text-white flex items-center justify-center text-2xl font-bold shadow-md shadow-indigo-100">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                {{ auth()->user()->role->label() }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ auth()->user()->email }}
                            </span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver al inicio
                </a>
            </div>
        </div>

        {{-- Formulario 1: Información personal y de cuenta --}}
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        {{-- Formulario 2: Actualización de contraseña --}}
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-200">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        {{-- Formulario 3: Zona de peligro / Eliminar cuenta --}}
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-red-200">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
