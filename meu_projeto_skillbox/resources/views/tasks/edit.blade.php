@extends('layouts.skillboxApp')

@section('title', 'Editar Tarefa')
@section('titulo', 'Editar Tarefa')

@section('content')
    <h2>Editar tarefa do projeto: {{ $canvas->titulo }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tasks.update', ['canvas' => $canvas->id, 'task' => $task->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo', $task->titulo) }}" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="4" class="form-control">{{ old('descricao', $task->descricao) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-select" required>
                <option value="pendente" {{ (old('status', $task->status) === 'pendente') ? 'selected' : '' }}>Pendente</option>
                <option value="concluida" {{ (old('status', $task->status) === 'concluida') ? 'selected' : '' }}>Concluída</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="{{ route('tasks.index', ['canvas' => $canvas->id]) }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection
