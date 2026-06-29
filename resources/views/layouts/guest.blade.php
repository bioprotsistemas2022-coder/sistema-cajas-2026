<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bioimplant — Smart Surgery Management</title>
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

            {{ $slot }}
        </div>
    </body>
</html>
