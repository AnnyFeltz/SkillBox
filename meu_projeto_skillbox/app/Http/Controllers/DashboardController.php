<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CanvasProjeto;
use App\Models\Tool;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Trabalhos publicados de todos os usuários (públicos)
        $publicados = CanvasProjeto::where('is_public', true)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Ferramentas do usuário logado
        $ferramentas = Tool::where('user_id', $userId)->get();

        // Projetos do usuário logado (públicos e privados)
        $meusProjetos = CanvasProjeto::where('user_id', $userId)
            ->latest()
            ->get();

        return view('dashboard', compact('publicados', 'ferramentas', 'meusProjetos'));
    }
}
