@extends('layouts.skillboxApp')

@section('title', 'Confirmar Senha')
@section('titulo', 'Confirmação de Senha')

@section('content')
    <p class="info-text">
        Esta é uma área segura do sistema. Por favor, confirme sua senha antes de continuar.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}" >
        @csrf

        <label for="password">Senha</label>
        <input 
            id="password" 
            name="password" 
            type="password" 
            required 
            autocomplete="current-password"
            class="input-field"
        />
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit" class="btn-primary mt-4">Confirmar</button>
    </form>
@endsection
