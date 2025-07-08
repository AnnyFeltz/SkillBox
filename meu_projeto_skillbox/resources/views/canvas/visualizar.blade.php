@extends('layouts.skillboxApp')

@section('title', 'Visualizar Projeto: ' . $canvas->titulo)

@section('content')
<div class="container mt-4">
    <h2>Visualizar Projeto: {{ $canvas->titulo }}</h2>
    <p><strong>Autor:</strong> {{ $canvas->user->name }}</p>

    @if ($canvas->png_url)
        <div style="text-align:center;">
            <img src="{{ $canvas->png_url }}" alt="Imagem do projeto {{ $canvas->titulo }}" style="max-width: 100%; height: auto; border: 1px solid #ccc; padding: 10px; background: #f9f9f9;">
        </div>
    @else
        <p class="text-danger">Nenhuma imagem disponível para este projeto.</p>
    @endif

    <div class="mt-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Voltar</a>
    </div>
</div>
@endsection
