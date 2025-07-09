@extends('layouts.skillboxApp')

@section('title', 'Visualizar Projeto')

@section('titulo', 'Visualizar Projeto: ' . $canvas->titulo)

@section('content')
<div class="container">
    <h2 class="mb-3">{{ $canvas->titulo }} <small class="text-muted">por {{ $canvas->user->name }}</small></h2>

    @if($canvas->preview_url)
    @php
        // Calcula proporção para manter o aspecto real do canvas
        $aspectRatio = ($canvas->height ?? 1) > 0 ? ($canvas->width / $canvas->height) : 1;
    @endphp

    <div style="display: flex; justify-content: center; align-items: center; border: 1px solid #ccc; padding: 10px;">
        <div style="
            width: 100%;
            max-width: min(100%, {{ $canvas->width }}px);
            aspect-ratio: {{ $aspectRatio }};
            overflow: hidden;
            position: relative;
        ">
            <img src="{{ $canvas->preview_url }}" alt="Preview do projeto"
                 style="width: 100%; height: 100%; object-fit: contain; position: absolute; top: 0; left: 0;">
        </div>
    </div>
    @else
    <p class="text-muted">Nenhuma imagem disponível.</p>
    @endif

    @if($canvas->tools->count())
    <div class="mt-4">
        <h5>🛠️ Ferramentas utilizadas neste projeto:</h5>
        <ul class="list-group">
            @foreach($canvas->tools as $tool)
            <li class="list-group-item">
                <strong>{{ $tool->nome }}</strong>
                @if($tool->descricao)
                <br><small class="text-muted">{{ $tool->descricao }}</small>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    @else
    <p class="text-muted mt-4">Nenhuma ferramenta associada a este projeto.</p>
    @endif

    <div class="mt-3">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Voltar</a>
    </div>
</div>
@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas/publicados"><i class="icon-user material-symbols-outlined">gallery_thumbnail</i> <span class="label">Galeria</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
@endsection
