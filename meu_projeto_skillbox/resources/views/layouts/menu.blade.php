<aside class="sidebar collapsed" id="sidebar">
    <div class="logo">
        <button id="toggle-sidebar" aria-label="Abrir/fechar menu" aria-expanded="false">
            <span class="material-symbols-outlined menu-icon">menu</span>
        </button>
        <h2>MeuApp</h2>
    </div>

    <ul class="menu-list">
        <div class="menu-options">
            @yield('menu-items')
        </div>
            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                    <i class="icon-logout material-symbols-outlined">logout</i> <span class="label">Sair</span>
                </a>

                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display:none;">
                    @csrf
                </form>
            </li>
    </ul>
</aside>