@extends('layouts.skillboxApp')

@section('title', 'Criar Ferramenta')
@section('titulo', 'Criar Nova Ferramenta')

@section('content')

<div class="container mt-4">
    <h2>Criar Nova Ferramenta</h2>

    <form action="{{ route('tools.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nome" class="form-label">Nome da Ferramenta</label>
            <input type="text" class="form-control" id="nome" name="nome" required>
        </div>
        <button type="submit" class="btn btn-primary">Criar</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

@endsection
