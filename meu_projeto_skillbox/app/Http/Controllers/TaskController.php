<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\CanvasProjeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index($canvasId)
    {
        $canvas = CanvasProjeto::findOrFail($canvasId);
        $this->authorizeOwnership($canvas->user_id);

        $tasks = Task::where('canvas_projeto_id', $canvas->id)->get();
        return view('tasks.index', compact('tasks', 'canvas'));
    }

    public function create($canvasId)
    {
        $canvas = CanvasProjeto::findOrFail($canvasId);
        $this->authorizeOwnership($canvas->user_id);

        return view('tasks.create', compact('canvas'));
    }

    public function store(Request $request, $canvasId)
    {
        $canvas = CanvasProjeto::findOrFail($canvasId);
        $this->authorizeOwnership($canvas->user_id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        Task::create([
            'canvas_projeto_id' => $canvas->id,
            'titulo' => $request->titulo,
            'descricao' => $request->descricao,
            'status' => 'pendente',
        ]);

        return redirect()->route('tasks.index', $canvas->id)
            ->with('success', 'Tarefa criada com sucesso!');
    }

    public function edit($canvasId, $id)
    {
        $canvas = CanvasProjeto::findOrFail($canvasId);
        $this->authorizeOwnership($canvas->user_id);

        $task = Task::findOrFail($id);
        return view('tasks.edit', compact('task', 'canvas'));
    }

    public function update(Request $request, $canvasId, $id)
    {
        $canvas = CanvasProjeto::findOrFail($canvasId);
        $this->authorizeOwnership($canvas->user_id);

        $task = Task::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'status' => 'required|in:pendente,concluida',
        ]);

        $task->update($request->only('titulo', 'descricao', 'status'));

        return redirect()->route('tasks.index', $canvas->id)
            ->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function destroy($canvasId, $id)
    {
        $canvas = CanvasProjeto::findOrFail($canvasId);
        $this->authorizeOwnership($canvas->user_id);

        $task = Task::findOrFail($id);
        $task->delete();

        return redirect()->route('tasks.index', $canvas->id)
            ->with('success', 'Tarefa excluída com sucesso!');
    }

    private function authorizeOwnership($ownerId)
    {
        if ($ownerId !== Auth::id()) {
            abort(403, 'Não autorizado');
        }
    }
}
