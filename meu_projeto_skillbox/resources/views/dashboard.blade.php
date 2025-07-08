@extends('layouts.skillboxApp')

@section('title', 'Dashboard')
@section('titulo')
Bem-vind@, {{ Auth::user()->name }}
@endsection

@section('content')
<div class="container mt-4">
    {{-- Trabalhos Publicados --}}
    <h3>🌐 Trabalhos Publicados</h3>
    @if ($publicados->isEmpty())
    <p class="text-muted">Nenhum projeto publicado ainda.</p>
    @else
    <div class="d-flex align-items-center overflow-auto gap-2 mb-4">
        {{-- Botão criar vazio aqui porque não criamos trabalhos publicados --}}

        @foreach ($publicados->take(6) as $canvas)
        <div class="card" style="min-width: 180px; max-width: 180px; flex-shrink: 0;">
            <div class="card-header text-center p-1">
                <small><strong>{{ $canvas->user->name }}</strong></small>
            </div>
            <div class="card-body p-2">
                <div id="preview-{{ $canvas->id }}" class="mini-preview mb-1" data-canvas-id="{{ $canvas->id }}" 
                     style="aspect-ratio: {{ $canvas->height != 0 ? ($canvas->width / $canvas->height) : 1 }}; width: 100%; border: 1px solid #ccc;">
                </div>
                <h6 class="card-title text-truncate" title="{{ $canvas->titulo }}">{{ $canvas->titulo }}</h6>
                {{-- Botão para visualizar PNG do projeto publicado --}}
                <a href="{{ route('canvas.visualizar', $canvas->id) }}" class="btn btn-sm btn-outline-primary w-100" target="_blank" rel="noopener noreferrer">Visualizar</a>
            </div>
        </div>
        @endforeach
    </div>
    @if ($publicados->count() > 6)
    <div class="text-end mt-2 mb-4">
        <a href="{{ route('canvas.publicados') }}" class="btn btn-secondary">Ver Todos</a>
    </div>
    @endif
    @endif

    <hr>

    {{-- Ferramentas --}}
    <h3>🛠️ Suas Ferramentas</h3>
    <div class="d-flex align-items-center overflow-auto gap-2 mb-4">
        {{-- Botão criar ferramenta --}}
        <a href="{{ route('tools.create') }}" class="btn btn-primary btn-circle flex-shrink-0" title="Criar Nova Ferramenta">+</a>

        {{-- Ferramentas listadas em linha --}}
        @forelse ($ferramentas as $tool)
        <div class="card px-2 py-1" style="min-width: 140px; max-width: 140px; flex-shrink: 0; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <span class="text-truncate w-100 text-center" title="{{ $tool->nome }}">{{ $tool->nome }}</span>
        </div>
        @empty
        <span class="text-muted ms-2">Nenhuma ferramenta ainda.</span>
        @endforelse
    </div>

    <hr>

    {{-- Seus Projetos --}}
    <h3>🎨 Seus Projetos</h3>
    <div class="d-flex align-items-center overflow-auto gap-2 mb-4">
        {{-- Botão criar projeto igual do outro blade --}}
        <button id="btn-new-canvas" class="btn btn-success mt-3">➕ Novo Canvas</button>

        @forelse ($meusProjetos as $canvas)
        <div class="card" style="min-width: 180px; max-width: 180px; flex-shrink: 0;">
            <div class="card-body p-2">
                <div id="preview-{{ $canvas->id }}" class="mini-preview mb-1" data-canvas-id="{{ $canvas->id }}" 
                     style="aspect-ratio: {{ $canvas->height != 0 ? ($canvas->width / $canvas->height) : 1 }}; width: 100%; border: 1px solid #ccc;">
                </div>
                <h6 class="card-title text-truncate" title="{{ $canvas->titulo }}">{{ $canvas->titulo }}</h6>
                <span class="badge bg-{{ $canvas->is_public ? 'success' : 'secondary' }}">
                    {{ $canvas->is_public ? 'Publicado' : 'Privado' }}
                </span>
                {{-- Botão Editar --}}
                <a href="{{ url('/editor?id=' . $canvas->id) }}" class="btn btn-sm btn-primary mt-2">Editar</a>
            </div>
        </div>
        @empty
        <span class="text-muted ms-2">Você ainda não criou nenhum projeto.</span>
        @endforelse
    </div>

    @if ($meusProjetos->count() > 6)
    <div class="text-end mt-2">
        <a href="{{ route('canvas.index') }}" class="btn btn-outline-secondary">Ver Todos</a>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .btn-circle {
        border-radius: 50%;
        width: 40px;
        height: 40px;
        padding: 0;
        font-size: 28px;
        text-align: center;
        line-height: 38px;
        display: inline-block;
    }

    .mini-preview {
        background-color: #f9f9f9;
    }
    .overflow-auto::-webkit-scrollbar {
        height: 8px;
    }
    .overflow-auto::-webkit-scrollbar-thumb {
        background-color: #bbb;
        border-radius: 4px;
    }
    .overflow-auto::-webkit-scrollbar-track {
        background-color: #eee;
    }
</style>
@endpush

@push('scripts')
<script>
document.getElementById('btn-new-canvas')?.addEventListener('click', () => {
    window.location.href = '/editor?new=true';
});
</script>
@endpush
