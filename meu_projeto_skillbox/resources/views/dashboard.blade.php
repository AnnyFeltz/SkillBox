@extends('layouts.skillboxApp')

@section('title', 'Dashboard')

@section('titulo', 'Dashboard')

@section('content')

<div class='teste'>
    TESTE
</div>
HOME USUARIOS

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <x-dropdown-link :href="route('profile.edit')">
        {{ __('Profile') }}
    </x-dropdown-link>

    <x-dropdown-link :href="route('logout')"
        onclick="event.preventDefault();
        this.closest('form').submit();">
        {{ __('Log Out') }}
    </x-dropdown-link>
</form>
@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
@endsection