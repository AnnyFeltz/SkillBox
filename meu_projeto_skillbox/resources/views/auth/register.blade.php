@extends('layouts.skillboxApp')

@section('title', 'Registrar')
@section('titulo', 'Criar Conta')

@section('content')
<form method="POST" action="{{ route('register') }}" >
    @csrf

    <label for="name">Nome</label>
    <input 
        id="name" 
        name="name" 
        type="text" 
        value="{{ old('name') }}" 
        required 
        autofocus 
        autocomplete="name" 
        class="input-field"
    />
    @error('name') <div class="error">{{ $message }}</div> @enderror

    <label for="email" class="mt-4">Email</label>
    <input 
        id="email" 
        name="email" 
        type="email" 
        value="{{ old('email') }}" 
        required 
        autocomplete="username" 
        class="input-field"
    />
    @error('email') <div class="error">{{ $message }}</div> @enderror

    <label for="password" class="mt-4">Senha</label>
    <input 
        id="password" 
        name="password" 
        type="password" 
        required 
        autocomplete="new-password" 
        class="input-field"
    />
    @error('password') <div class="error">{{ $message }}</div> @enderror

    <label for="password_confirmation" class="mt-4">Confirmar Senha</label>
    <input 
        id="password_confirmation" 
        name="password_confirmation" 
        type="password" 
        required 
        autocomplete="new-password" 
        class="input-field"
    />
    @error('password_confirmation') <div class="error">{{ $message }}</div> @enderror

    <div class="flex justify-between items-center mt-6">
        <a href="{{ route('login') }}" class="link">Já registrado?</a>
        <button type="submit" class="btn-primary">Registrar</button>
    </div>
</form>
@endsection

@section('menu-items')
<li><a href="/"><i class="icon-dashboard material-symbols-outlined">home</i> <span class="label">Inicio</span></a></li>
<li><a href="/login"><i class="icon-user material-symbols-outlined">login</i> <span class="label">Entrar</span></a></li>
<li><a href="/register"><i class="icon-user material-symbols-outlined">person_add</i> <span class="label">Registrar</span></a></li>
@endsection
