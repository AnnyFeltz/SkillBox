<nav class="navbar">
    <h1>@yield('titulo', 'Bem-vind@')</h1>

    <div class="user-info">
        <span class="material-symbols-outlined user-icon">account_circle</span>

        <div class="dropdown-menu">
            <a href="/profile">Perfil</a>

            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-navbar').submit();">
                Sair
            </a>

            <form id="logout-form-navbar" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>
</nav>