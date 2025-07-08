<?php

namespace App\Http\Controllers;

use App\Models\CanvasProjeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Tool;

class CanvasProjetoController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $canvases = CanvasProjeto::where('user_id', $userId)->latest()->get();
        return view('canvas.index', compact('canvases'));
    }

    public function editor(Request $request)
    {
        $id = $request->query('id');
        $new = $request->query('new');
        $width = (int) $request->query('width', 1000);
        $height = (int) $request->query('height', 600);

        if ($new) {
            $canvas = CanvasProjeto::create([
                'titulo' => 'Projeto novo',
                'data_json' => json_encode([
                    'pages' => [[
                        'id' => 1,
                        'width' => $width,
                        'height' => $height,
                        'shapes' => [],
                    ]],
                    'activePageIndex' => 0,
                ]),
                'width' => $width,
                'height' => $height,
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('editor', ['id' => $canvas->id]);
        }

        if ($id) {
            $canvas = CanvasProjeto::with(['tools'])->where('user_id', Auth::id())->findOrFail($id);
        } else {
            abort(404, 'Projeto não encontrado');
        }

        $tasks = Task::where('canvas_projeto_id', $canvas->id)->get();
        $tools = $canvas->tools;
        $apiKey = env('IMGBB_API_KEY');

        return view('editor', compact('canvas', 'tasks', 'tools', 'apiKey'));
    }

    public function salvar(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'data_json' => 'required|string',
            'width' => 'required|integer|min:100',
            'height' => 'required|integer|min:100',
            'id' => 'nullable|integer|exists:canvas_projetos,id',
        ]);

        $dataJson = $request->input('data_json');

        if ($request->id) {
            $canvas = CanvasProjeto::where('user_id', $userId)->findOrFail($request->id);
            $canvas->update([
                'titulo' => $request->input('titulo'),
                'data_json' => $dataJson,
                'width' => $request->input('width'),
                'height' => $request->input('height'),
            ]);
        } else {
            $canvas = CanvasProjeto::create([
                'user_id' => $userId,
                'titulo' => $request->input('titulo') ?: 'Projeto salvo',
                'data_json' => $dataJson,
                'width' => $request->input('width'),
                'height' => $request->input('height'),
            ]);
        }

        return response()->json(['success' => true, 'id' => $canvas->id]);
    }

    public function carregar(Request $request)
    {
        $userId = Auth::id();
        $id = $request->query('id');

        if (!$id) {
            return response()->json(['error' => 'ID do projeto não informado'], 400);
        }

        $canvas = CanvasProjeto::where('user_id', $userId)->find($id);

        if (!$canvas) {
            return response()->json(['error' => 'Projeto não encontrado'], 404);
        }

        $data = [];
        if (filter_var($canvas->data_json, FILTER_VALIDATE_URL)) {
            try {
                $response = file_get_contents($canvas->data_json);
                $data = json_decode($response, true);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Erro ao carregar JSON do Mocky'], 500);
            }
        } else {
            $data = json_decode($canvas->data_json, true);
        }

        return response()->json([
            'data' => [
                'pages' => $data['pages'] ?? [],
                'activePageIndex' => $data['activePageIndex'] ?? 0,
            ],
            'width' => $canvas->width,
            'height' => $canvas->height,
            'id' => $canvas->id,
            'titulo' => $canvas->titulo,
        ]);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $canvas = CanvasProjeto::where('user_id', $userId)->findOrFail($id);
        $canvas->delete();

        return redirect()->route('canvas.index')->with('success', 'Projeto deletado com sucesso.');
    }

    public function adicionarTool(Request $request, $canvasId)
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
        ]);

        $canvas = CanvasProjeto::where('user_id', Auth::id())->findOrFail($canvasId);
        $tool = Tool::findOrFail($request->tool_id);

        if ($tool->user_id !== null && $tool->user_id !== $canvas->user_id) {
            return redirect()->back()->with('error', 'Você não pode adicionar esta ferramenta ao projeto.');
        }

        $canvas->tools()->syncWithoutDetaching([$tool->id]);

        return redirect()->back()->with('success', 'Ferramenta adicionada ao projeto!');
    }

    public function removerTool(Request $request, $canvasId, $toolId)
    {
        $canvas = CanvasProjeto::where('user_id', Auth::id())->findOrFail($canvasId);

        if (!$canvas->tools()->where('tool_id', $toolId)->exists()) {
            return redirect()->back()->with('error', 'Ferramenta não está associada ao projeto.');
        }

        $canvas->tools()->detach($toolId);

        return redirect()->back()->with('success', 'Ferramenta removida do projeto!');
    }

    public function visualizar($id)
    {
        $canvas = CanvasProjeto::findOrFail($id);
        return view('canvas.visualizar', compact('canvas'));
    }
}
