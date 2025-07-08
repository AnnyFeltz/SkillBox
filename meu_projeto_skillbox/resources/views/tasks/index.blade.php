@extends('layouts.skillboxApp')

@section('title', 'Lista de Tarefas')
@section('titulo', 'Tarefas do Projeto')

@section('content')
<h2 class="mb-4">Tarefas do projeto: {{ $canvas->titulo }}</h2>

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<a href="{{ route('tasks.create', ['canvas' => $canvas->id]) }}" class="btn btn-primary mb-3">➕ Adicionar nova tarefa</a>

@if ($tasks->isEmpty())
<p>Não há tarefas para este projeto.</p>
@else
<ul class="list-group">
    @foreach ($tasks as $task)
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <div style="word-break: break-word; max-width: 800px;">
                <strong>{{ $task->titulo }}</strong><br>
                <small>{{ $task->descricao }}</small>
            </div>
            <span class="badge bg-{{ $task->status === 'concluida' ? 'success' : 'warning text-dark' }}">
                {{ ucfirst($task->status) }}
            </span>
        </div>

        <div class="btn-group">
            <a href="{{ route('tasks.edit', ['canvas' => $canvas->id, 'task' => $task->id]) }}" class="btn btn-outline-secondary btn-sm">
                ✏️ Editar
            </a>

            <form action="{{ route('tasks.update', ['canvas' => $canvas->id, 'task' => $task->id]) }}" method="POST" style="display:inline;">
                @csrf
                @method('PUT')
                <input type="hidden" name="titulo" value="{{ $task->titulo }}">
                <input type="hidden" name="descricao" value="{{ $task->descricao }}">
                <input type="hidden" name="status" value="{{ $task->status === 'pendente' ? 'concluida' : 'pendente' }}">
                <button type="submit" class="btn btn-outline-info btn-sm">
                    🔄 {{ $task->status === 'pendente' ? 'Concluir' : 'Reabrir' }}
                </button>
            </form>

            <form action="{{ route('tasks.destroy', ['canvas' => $canvas->id, 'task' => $task->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta tarefa?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-sm">🗑️</button>
            </form>
        </div>
    </li>
    @endforeach
</ul>
@endif

<a href="{{ route('editor', ['id' => $canvas->id]) }}" class="btn btn-outline-primary mt-3">
    🖌️ Voltar ao Editor Visual
</a>
@endsection