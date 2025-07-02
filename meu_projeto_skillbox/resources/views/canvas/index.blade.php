@extends('layouts.skillboxApp')
@section('title', 'Meus Projetos')

@section('vite')
@vite('resources/js/app.js')
@endsection

@section('content')
<div class="container">
    <h1>🎨 Meus Projetos</h1>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($canvases->isEmpty())
    <p>Você ainda não tem nenhum canvas salvo.</p>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Título</th>
                <th>Data</th>
                <th>Ações</th>
                <th>Miniatura</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($canvases as $canvas)
            <tr>
                <td>{{ $canvas->titulo }}</td>
                <td>{{ $canvas->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ url('/editor?id=' . $canvas->id) }}" class="btn btn-sm btn-primary">Abrir</a>

                    <form action="{{ route('canvas.destroy', $canvas->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</button>
                    </form>
                </td>
                <td>
                    <div id="preview-{{ $canvas->id }}"
                        class="mini-preview"
                        data-canvas-id="{{ $canvas->id }}"
                        style="aspect-ratio: {{ $canvas->height != 0 ? ($canvas->width / $canvas->height) : 1 }}; width: 150px; border: 1px solid #ccc; overflow: hidden;"></div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <button id="btn-new-canvas" class="btn btn-success mt-3">➕ Novo Canvas</button>
</div>
@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
@endsection