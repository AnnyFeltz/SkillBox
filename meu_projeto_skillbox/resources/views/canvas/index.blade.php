@extends('layouts.skillboxApp')

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
                        style="aspect-ratio: {{ $canvas->width }} / {{ $canvas->height }}; width: 150px; max-height: 90px; border: 1px solid #ccc; overflow: hidden;
                        "></div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <button id="btn-new-canvas" class="btn btn-success mt-3">➕ Novo Canvas</button>
</div>
@endsection