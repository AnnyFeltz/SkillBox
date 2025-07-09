<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CanvasProjeto;
use App\Models\Tool;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            return $this->adminDashboard();
        }

        $userId = Auth::id();

        $publicados = CanvasProjeto::where('is_public', true)
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        $ferramentas = Tool::where('user_id', $userId)->get();

        $meusProjetos = CanvasProjeto::where('user_id', $userId)
            ->latest()
            ->get();

        return view('dashboard', compact('publicados', 'ferramentas', 'meusProjetos'));
    }

    public function adminDashboard()
    {
        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'totalProjetosPublicos' => CanvasProjeto::where('is_public', true)->count(),
            'totalProjetos' => CanvasProjeto::count(),
            'totalFerramentas' => Tool::count(),
            'users' => User::orderBy('created_at', 'desc')->paginate(10),
            'publicados' => CanvasProjeto::where('is_public', true)->latest()->limit(12)->get(),
            'ferramentas' => Tool::latest()->limit(12)->get(),
        ]);
    }

    public function perfil()
    {
        $user = Auth::user();

        $totalProjetosUsuario = CanvasProjeto::where('user_id', $user->id)->count();
        $totalFerramentasUsuario = Tool::where('user_id', $user->id)->count();
        $ferramentas = Tool::where('user_id', $user->id)->get();

        return view('profile.index', compact(
            'user',
            'totalProjetosUsuario',
            'totalFerramentasUsuario',
            'ferramentas'
        ));
    }
}
