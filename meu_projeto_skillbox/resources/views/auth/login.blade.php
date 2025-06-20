@extends('layouts.skillboxApp')

@section('title', 'Login')
@section('titulo', 'Entrar')

@section('content')
    @if(session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">Email</label>
        <input 
            id="email" 
            name="email" 
            type="email" 
            value="{{ old('email') }}" 
            required 
            autofocus 
            autocomplete="username" 
            class="input-field"
        />
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password" class="mt-4">Senha</label>
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

        <label class="remember-me">
            <input type="checkbox" name="remember" id="remember_me" />
            Lembrar-me
        </label>

        <div class="flex justify-between items-center mt-4">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link">Esqueceu a senha?</a>
            @endif

            <button type="submit" class="btn-primary">Entrar</button>
        </div>
    </form>
@endsection

@section('menu-items')
<li><a href="/"><i class="icon-dashboard material-symbols-outlined">home</i> <span class="label">Inicio</span></a></li>
<li><a href="/login"><i class="icon-user material-symbols-outlined">login</i> <span class="label">Entrar</span></a></li>
<li><a href="/register"><i class="icon-user material-symbols-outlined">person_add</i> <span class="label">Registrar</span></a></li>
@endsection
