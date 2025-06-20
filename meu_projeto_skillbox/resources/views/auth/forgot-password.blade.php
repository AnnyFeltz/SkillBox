@extends('layouts.skillboxApp')

@section('title', 'Recuperar Senha')
@section('titulo', 'Esqueceu a Senha?')

@section('content')
    <p class="info-text">
        Esqueceu sua senha? Sem problema. Informe seu email e enviaremos um link para você criar uma nova senha.
    </p>

    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" >
        @csrf

        <label for="email">Email</label>
        <input 
            id="email" 
            name="email" 
            type="email" 
            value="{{ old('email') }}" 
            required 
            autofocus 
            class="input-field"
        />
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn-primary mt-4">Enviar Link de Reset</button>
    </form>
@endsection
