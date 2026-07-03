<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

    <title inertia>{{ config('app.name', 'Molka Moden') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&family=Inter:wght@100..900&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite('resources/js/app.js')

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#b45309">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Meuble Driss">
    <link rel="apple-touch-icon" href="/icon-512.png">

    @inertiaHead
</head>

<body class="font-sans antialiased bg-[#fdfdfc] text-[#1b1b18]">
    @inertia

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').then(registration => {
                    console.log('SW registered:', registration.scope);
                }).catch(error => {
                    console.log('SW registration failed:', error);
                });
            });
        }
    </script>
</body>

</html>