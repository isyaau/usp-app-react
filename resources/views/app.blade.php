<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'KSP KOPINKA') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/react.css', 'resources/js/app.jsx'])
</head>

<body class="font-sans antialiased bg-slate-100">
    @inertia
</body>

</html>
