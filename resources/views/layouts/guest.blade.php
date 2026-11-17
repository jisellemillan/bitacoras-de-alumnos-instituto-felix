<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Monitor Psicopedagógico') }} · Instituto Félix</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/main.css', 'resources/js/app.js'])
        @livewireStyles
        <link rel="icon" href="{{ asset('img/logo-felix.jpg') }}" type="image/jpeg">
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="auth-layout">
            <div class="auth-card">
                <div class="auth-logo">
                    <x-application-logo class="w-24 h-24 shadow-lg" />
                    <div>
                        <h1 class="auth-title">Monitor Psicopedagógico</h1>
                        <p class="auth-subtitle">Instituto Félix de Jesús Rougier</p>
                    </div>
                </div>

                {{ $slot }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>
