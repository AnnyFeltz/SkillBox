@extends('layouts.skillboxApp')

@section('title', 'Resetar Senha')
@section('titulo', 'Nova Senha')

@section('content')
<form method="POST" action="{{ route('password.store') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <label for="email">Email</label>
    <input 
        id="email" 
        name="email" 
        type="email" 
        value="{{ old('email', $request->email) }}" 
        required 
        autofocus 
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

    <div class="mt-6 flex justify-end">
        <button type="submit" class="btn-primary">Resetar Senha</button>
    </div>
</form>
@endsection
