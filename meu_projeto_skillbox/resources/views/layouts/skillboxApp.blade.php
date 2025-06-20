<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'SkillBox')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/editor.js'])
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