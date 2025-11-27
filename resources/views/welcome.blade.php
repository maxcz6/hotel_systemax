<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Hotel Systemax</title>
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    </head>
    <body>
        <div class="guest-layout">
            <div class="guest-card" style="text-align: center;">
                <h1 style="font-size: 2rem; margin-bottom: 1rem;">Hotel Systemax</h1>
                <p style="margin-bottom: 2rem; color: #4b5563;">Bienvenido a nuestro Sistema de Gestión Hotelera.</p>
                
                @if (Route::has('login'))
                    <div class="flex justify-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn">Panel de Control</a>
                        @else
                            <a href="{{ route('login') }}" class="btn">Iniciar Sesión</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn" style="background-color: white; color: #1f2937; border: 1px solid #d1d5db;">Registrarse</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </body>
</html>
