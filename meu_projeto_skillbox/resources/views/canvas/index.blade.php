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
            </tr>
        </thead>
        <tbody>
            @foreach ($canvases as $canvas)
            <tr>
                <td>{{ $canvas->title }}</td>
                <td>{{ $canvas->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <a href="{{ url('/editor?id=' . $canvas->id) }}" class="btn btn-sm btn-primary">Abrir</a>

                    <form action="{{ route('canvas.destroy', $canvas->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <button id="btn-new-canvas" class="btn btn-success mt-3">➕ Novo Canvas</button>

    <script>
        document.getElementById('btn-new-canvas').addEventListener('click', () => {
            let width = prompt('Digite a largura do canvas (mínimo 100):', '1000');
            let height = prompt('Digite a altura do canvas (mínimo 100):', '600');

            width = parseInt(width);
            height = parseInt(height);

            if (!width || width < 100) width = 1000;
            if (!height || height < 100) height = 600;

            window.location.href = `/editor?new=1&width=${width}&height=${height}`;
        });
    </script>


</div>
@endsection