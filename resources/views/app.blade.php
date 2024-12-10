<!DOCTYPE html>
<html lang="{{ $locale =  str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="locale" content="{{ $locale }}"/>

    <title inertia>{{ config('laraprime.name', 'LaraPrime') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @viteReactRefresh
    @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
    @inertiaHead
    <script>
        if (localStorage.primeTheme === 'dark' || (!('primeTheme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="font-sans antialiased">
    @inertia

    <!-- Build LaraPrime Instance -->
    <script type="module">
        const config = @json(\Didix16\Laraprime\LaraPrime::jsonVariables(request()));
        window.LaraPrime = createLaraPrimeApp(config)
        LaraPrime.bootstrap();
    </script>

    <!-- Start LaraPrime -->
    <script type="module">
        LaraPrime.init();
    </script>
</body>
</html>
