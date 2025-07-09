@extends('layouts.skillboxApp')

@section('title', 'Perfil')
@section('titulo', 'Editar Perfil')

@section('content')
<div class="py-12 max-w-7xl mx-auto space-y-6 px-4 sm:px-6 lg:px-8">
    
    <div class="card">
        @include('profile.partials.update-profile-information-form')
    </div>

    <div class="card">
        @include('profile.partials.update-password-form')
    </div>

    <div class="card">
        @include('profile.partials.delete-user-form')
    </div>

</div>
@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
@endsection