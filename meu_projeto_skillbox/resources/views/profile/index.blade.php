@extends('layouts.skillboxApp')

@section('title', 'Perfil')
@section('titulo', '👤 Meu Perfil')

@section('content')
<div class="container mt-4">
    <div class="row g-4">
        {{-- CARD DO PERFIL --}}
        <div class="col-md-4">
            <div class="card text-center p-4 position-relative">

                {{-- Botão de editar perfil no topo direito --}}
                <a href="{{ route('profile.edit') }}" 
                   class="position-absolute top-0 end-0 m-2 text-decoration-none text-dark" 
                   title="Editar Perfil">
                    <span class="material-symbols-outlined">edit</span>
                </a>

                <span class="material-symbols-outlined" style="font-size: 80px; color: #4f46e5;">
                    account_circle
                </span>
                <h4 class="mt-3">{{ Auth::user()->name }}</h4>
                <p class="text-muted">{{ Auth::user()->email }}</p>

                <hr>

                <h6 class="text-muted">Projetos Criados</h6>
                <p class="fs-3 fw-bold">{{ $totalProjetosUsuario }}</p>

                <h6 class="text-muted">Ferramentas Criadas</h6>
                <p class="fs-5">{{ $totalFerramentasUsuario }}</p>
            </div>
        </div>

        {{-- CARD DE FERRAMENTAS --}}
        <div class="col-md-8">
            <div class="card p-4">
                <h5 class="mb-3">🛠️ Suas Ferramentas</h5>

                <div class="d-flex flex-wrap gap-2">
                    @forelse ($ferramentas as $tool)
                        <div class="badge bg-secondary text-wrap p-2">{{ $tool->nome }}</div>
                    @empty
                        <span class="text-muted">Você ainda não criou nenhuma ferramenta.</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
<li><a href="/tools"><i class="icon-user material-symbols-outlined">construction</i> <span class="label">Ferramentas</span></a></li>
@endsection
