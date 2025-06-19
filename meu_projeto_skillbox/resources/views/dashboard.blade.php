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