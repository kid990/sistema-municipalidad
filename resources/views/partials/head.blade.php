<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@php
    $appName = config('app.name', 'Sistema Municipal');
    $pageTitle = filled($title ?? null) ? $title.' - '.$appName : $appName;
    $description = $description ?? 'Sistema municipal para la gestion de ciudadanos, comuneros, cargos, lotes, faenas, asistencias y multas.';
    $currentUrl = url()->current();
@endphp

<title>
    {{ $pageTitle }}
</title>

<meta name="description" content="{{ $description }}" />
<meta name="robots" content="noindex, nofollow" />
<link rel="canonical" href="{{ $currentUrl }}" />

<meta property="og:locale" content="es_PE" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{{ $pageTitle }}" />
<meta property="og:description" content="{{ $description }}" />
<meta property="og:url" content="{{ $currentUrl }}" />
<meta property="og:site_name" content="{{ $appName }}" />

<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{{ $pageTitle }}" />
<meta name="twitter:description" content="{{ $description }}" />

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=roboto-serif:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=fira-sans:400,500,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.bunny.net/css?family=merriweather:400,700&display=swap" rel="stylesheet">

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
