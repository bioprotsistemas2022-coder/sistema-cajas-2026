<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sistema de Control y Seguimiento de Cajas de Cirugía</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="login-page">
        <div class="login-card">
            <div class="text-center mb-4">
                <img src="{{ asset('images/logo.png') }}" alt="Bioimplant" height="55" class="mb-3">
                <h2 class="brand-title mb-1">Bioimplant Trace</h2>
                <p class="brand-subtitle mb-0">Acceso Autorizado</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success py-2 px-3 mb-3 small">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger py-2 px-3 mb-3 small">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3 mb-3 small">
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            {{ $slot }}
        </div>
    </body>
</html>
