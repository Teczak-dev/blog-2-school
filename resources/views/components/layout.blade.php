<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blog - Lista Postów</title>

    <!-- Cache busting for dynamic content -->
    @if(session('cache_buster'))
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    @endif

    @if(app()->environment('local'))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/assets/app-CDN8Lmid.css') }}">
        <script type="module" src="{{ asset('build/assets/app-BFW7sici.js') }}"></script>
    @endif
</head>

<body class="h-full">
    @include('partials.navigation')

    {{ $slot }}

    @include('partials.footer')
</body>

</html>
