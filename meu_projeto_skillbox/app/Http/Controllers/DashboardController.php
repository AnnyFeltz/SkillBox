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
        $publicados = CanvasProjeto::where('is_public', true)
            ->with('user')
            ->latest()
            ->take(6)
            ->get();

        $ferramentas = Tool::where('user_id', Auth::id())->get();

        $meusProjetos = CanvasProjeto::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('dashboard', compact('publicados', 'ferramentas', 'meusProjetos'));
    }
}
