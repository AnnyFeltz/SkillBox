@extends('layouts.skillboxApp')

@section('title', 'Dashboard')

@section('titulo', 'Dashboard')

@section('content')

<form class="form">
    Hello
</form>

@endsection


@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
@endsection