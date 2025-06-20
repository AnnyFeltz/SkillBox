@extends('layouts.skillboxApp')

@section('title', 'Verifique seu Email')
@section('titulo', 'Verificação de Email')

@section('content')
    <p class="info-text">
        Obrigada por se cadastrar! Antes de continuar, por favor verifique seu endereço de email clicando no link que enviamos.
        Se não recebeu, podemos enviar outro.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert-success">
            Um novo link de verificação foi enviado para seu email.
        </div>
    @endif

    <div class="mt-6 flex justify-between items-center">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary">Reenviar Email de Verificação</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="link">Sair</button>
        </form>
    </div>
@endsection
