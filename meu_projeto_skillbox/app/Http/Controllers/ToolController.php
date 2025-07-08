<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::where(function ($query) {
            $query->where('is_global', true)
                ->orWhere('user_id', Auth::id());
        })->get();

        $apiKey = config('services.imgbb.key');

        return view('tools.index', compact('tools', 'apiKey'));
    }

    public function create()
    {
        return view('tools.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:tools,nome',
        ]);

        Tool::create([
            'nome' => $request->nome,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('tools.index')->with('success', 'Ferramenta criada com sucesso!');
    }

    public function edit($id)
    {
        $tool = Tool::findOrFail($id);

        if ($tool->is_global) {
            return redirect()->route('tools.index')->with('error', 'Ferramentas globais não podem ser editadas.');
        }

        $this->authorizeOwnership($tool->user_id);

        return view('tools.edit', compact('tool'));
    }

    public function update(Request $request, $id)
    {
        $tool = Tool::findOrFail($id);

        if ($tool->is_global) {
            return redirect()->route('tools.index')->with('error', 'Ferramentas globais não podem ser editadas.');
        }

        $this->authorizeOwnership($tool->user_id);

        $request->validate([
            'nome' => 'required|string|max:255|unique:tools,nome,' . $tool->id,
        ]);

        $tool->update($request->only('nome'));

        return redirect()->route('tools.index')->with('success', 'Ferramenta atualizada com sucesso!');
    }

    public function destroy($id)
    {
        $tool = Tool::findOrFail($id);

        if ($tool->is_global) {
            return redirect()->route('tools.index')->with('error', 'Ferramentas globais não podem ser excluídas.');
        }

        $this->authorizeOwnership($tool->user_id);

        $tool->delete();

        return redirect()->route('tools.index')->with('success', 'Ferramenta excluída com sucesso!');
    }

    private function authorizeOwnership($ownerId)
    {
        if ($ownerId === null || $ownerId !== Auth::id()) {
            abort(403, 'Não autorizado');
        }
    }
}