@extends('layouts.skillboxApp')

@section('title', 'Home')

@section('titulo', 'Bem vindo(a) ao SkillBox!')

@section('content')
    <div class="home-intro" style="padding: 20px; max-width: 700px; margin: auto; text-align: center;">
        <h2>Descubra, crie e organize seus portfólios e habilidades com o SkillBox</h2>
        <p>Este é o seu espaço para construir um portfólio técnico, controlar suas ferramentas e gamificar seu aprendizado.</p>
        <p>Faça seu cadastro ou entre para começar a explorar!</p>

        <div style="margin-top: 30px;">
            <a href="/register" class="btn btn-primary" style="margin-right: 10px;">Registrar</a>
            <a href="/login" class="btn btn-secondary">Entrar</a>
        </div>
    </div>
@endsection

@section('menu-items')
<li><a href="/"><i class="icon-dashboard material-symbols-outlined">home</i> <span class="label">Início</span></a></li>
<li><a href="/login"><i class="icon-user material-symbols-outlined">login</i> <span class="label">Entrar</span></a></li>
<li><a href="/register"><i class="icon-user material-symbols-outlined">person_add</i> <span class="label">Registrar</span></a></li>
@endsection
