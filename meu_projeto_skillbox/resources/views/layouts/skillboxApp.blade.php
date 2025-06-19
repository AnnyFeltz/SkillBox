<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>@yield('title')</title>
</head>

<body>
    <div class="conteiner">
        @include('layouts.menu')
        <div class="main">
            @include('layouts.navbar')
            <div class="content">
                @yield('content')
            </div>
        </div>
</body>

</html>