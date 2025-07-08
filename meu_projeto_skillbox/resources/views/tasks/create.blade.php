@extends('layouts.skillboxApp')

@section('title', 'Criar Tarefa')
@section('titulo', 'Criar Nova Tarefa')

@section('content')
    <h2>Criar nova tarefa para o projeto: {{ $canvas->titulo }}</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tasks.store', ['canvas' => $canvas->id]) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}" required>
        </div>

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição</label>
            <textarea name="descricao" id="descricao" rows="4" class="form-control">{{ old('descricao') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="{{ route('tasks.index', ['canvas' => $canvas->id]) }}" class="btn btn-secondary">Cancelar</a>
    </form>
@endsection
