<!DOCTYPE html>
<html lang="pl" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Lista Postów</title>

    @if(app()->environment('local'))
        @vite(['resources/css/app.css'])
    @else
        <link rel="stylesheet" href="{{ asset('build/assets/app-D6WyvjNw.css') }}">
        <script type="module" src="{{ asset('build/assets/app-Due1s3iS.js') }}"></script>
    @endif
</head>

<body class="h-full">
    @include('partials.navigation')

    {{ $slot }}

    @include('partials.footer')

    @vite(['resources/js/app.js'])
</body>

</html>
