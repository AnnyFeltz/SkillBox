<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'SkillBox')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css'], true)
    @yield('vite')
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
    </div>
</body>

</html>