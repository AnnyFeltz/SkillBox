@extends('layouts.skillboxApp')

@section('title', 'Editar Ferramenta')
@section('titulo', 'Editar Ferramenta')

@section('content')

<div class="container mt-4">
    <h2>Editar Ferramenta</h2>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('tools.update', $tool->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Ferramenta</label>
            <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $tool->nome) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="{{ route('tools.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@endsection
