@extends('layouts.skillboxApp')

@section('title', 'Projetos Publicados')

@section('titulo', '🌍 Projetos da Galera')

@section('content')
<div class="sb-container mt-4">
    @if ($publicados->isEmpty())
        <p class="text-center text-muted">Nenhum projeto publicado ainda.</p>
    @else
        <div class="row g-4 justify-content-center">
            @foreach ($publicados as $canvas)
            <div class="col-6 col-md-3"> {{-- 2 colunas em xs/sm, 4 colunas em md+ --}}
                <div class="sb-card h-100 d-flex flex-column">
                    <div class="sb-card-header text-center p-2 bg-light rounded-top">
                        <small class="fw-bold">{{ $canvas->user->name }}</small>
                    </div>
                    <div class="sb-card-body p-3 flex-grow-1 d-flex flex-column">
                        @if ($canvas->preview_url)
                            <img src="{{ $canvas->preview_url }}"
                                alt="Miniatura de {{ $canvas->titulo }}"
                                style="aspect-ratio: {{ $canvas->height != 0 ? ($canvas->width / $canvas->height) : 1 }};
                                    width: 100%;
                                    height: auto;
                                    object-fit: cover;
                                    border-radius: 0.25rem;
                                    border: 1px solid #ddd;"
                                class="mb-3 rounded">
                        @else
                            <div class="sb-mini-preview mb-3 d-flex align-items-center justify-content-center bg-light rounded" style="height: 180px; border: 1px solid #ddd;">
                                <span class="text-muted small">Sem preview</span>
                            </div>
                        @endif
                        <h5 class="sb-card-title mb-3 text-truncate" title="{{ $canvas->titulo }}">{{ $canvas->titulo }}</h5>
                        <a href="{{ route('canvas.visualizar', $canvas->id) }}" class="btn btn-primary btn-sm mt-auto">Visualizar</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{-- Paginação customizada --}}
            {{ $publicados->links('vendor.pagination.custom') }}
        </div>
    @endif
</div>
@endsection


@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas/publicados"><i class="icon-user material-symbols-outlined">gallery_thumbnail</i> <span class="label">Galeria</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
@endsection
