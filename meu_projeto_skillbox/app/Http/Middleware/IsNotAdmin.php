<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsNotAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->isAdmin()) {
            return $next($request);
        }

        return redirect('/admin')->with('error', 'Você está logado como administrador.');
    }
}
