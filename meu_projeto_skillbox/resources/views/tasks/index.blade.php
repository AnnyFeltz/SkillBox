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

        <div class="botoes-edit-delete">
            <form action="{{ route('tasks.edit', ['canvas' => $canvas->id, 'task' => $task->id]) }}" method="GET" style="display:inline;">
                <button type="submit" class="button-editar material-symbols-outlined" title="Editar tarefa">
                    edit
                </button>
            </form>

            <form action="{{ route('tasks.update', ['canvas' => $canvas->id, 'task' => $task->id]) }}" method="POST" class="d-inline">
                @csrf
                @method('PUT')
                <input type="hidden" name="titulo" value="{{ $task->titulo }}">
                <input type="hidden" name="descricao" value="{{ $task->descricao }}">
                <input type="hidden" name="status" value="{{ $task->status === 'pendente' ? 'concluida' : 'pendente' }}">

                <button type="submit"
                    class="button-status material-symbols-outlined {{ $task->status === 'pendente' ? 'btn-green' : 'btn-yellow' }}"
                    title="{{ $task->status === 'pendente' ? 'Concluir tarefa' : 'Reabrir tarefa' }}">
                    {{ $task->status === 'pendente' ? 'check' : 'schedule' }}
                </button>
            </form>


            <form action="{{ route('tasks.destroy', ['canvas' => $canvas->id, 'task' => $task->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta tarefa?')" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="button-deletar material-symbols-outlined">delete</button>
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

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas/publicados"><i class="icon-user material-symbols-outlined">gallery_thumbnail</i> <span class="label">Galeria</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
<li><a href="/tools"><i class="icon-user material-symbols-outlined">construction</i> <span class="label">Ferramentas</span></a></li>
@endsection