<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect x='10' y='30' width='80' height='40' rx='8' fill='%23CA8A04' stroke='%23A16207' stroke-width='3'/><text x='50' y='57' font-size='28' font-weight='bold' fill='%23FEF3C7' text-anchor='middle' font-family='Arial'>AU</text><rect x='18' y='25' width='64' height='8' rx='4' fill='%23EAB308'/><rect x='18' y='67' width='64' height='8' rx='4' fill='%23EAB308'/></svg>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" media="print" onload="this.media='all'" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
