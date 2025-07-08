@extends('layouts.skillboxApp')

@section('title', 'Ferramentas')
@section('titulo', 'Ferramentas Disponíveis')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h4>Suas Ferramentas</h4>
        <a href="{{ route('tools.create') }}" class="btn btn-primary">+ Nova Ferramenta</a>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($tools->isEmpty())
            <p class="text-muted">Nenhuma ferramenta disponível ainda.</p>
            @else
            <ul class="list-group">
                @foreach ($tools as $tool)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ $tool->nome }}</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('tools.edit', $tool->id) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('tools.destroy', $tool->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta ferramenta?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
@endsection