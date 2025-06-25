<?php

namespace App\Http\Controllers;

use App\Models\CanvasProjeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanvasProjetoController extends Controller
{
    // Mostrar lista dos projetos do usuário
    public function index()
    {
        $userId = Auth::id();
        $canvases = CanvasProjeto::where('user_id', $userId)->latest()->get();
        return view('canvas.index', compact('canvases'));
    }

    // Editor: criar novo ou abrir existente
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
            $canvas = CanvasProjeto::where('user_id', Auth::id())->findOrFail($id);
        } else {
            abort(404, 'Projeto não encontrado');
        }

        return view('editor', compact('canvas'));
    }

    // Salvar ou atualizar projeto via AJAX
    public function salvar(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'data_json' => 'required|string',
            'width' => 'required|integer|min:100',
            'height' => 'required|integer|min:100',
            'id' => 'nullable|integer|exists:canvas_projetos,id',
        ]);

        $id = $request->input('id');

        if ($id) {
            $canvas = CanvasProjeto::where('user_id', $userId)->findOrFail($id);
            $canvas->update([
                'data_json' => $request->input('data_json'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
            ]);
        } else {
            $canvas = CanvasProjeto::create([
                'user_id' => $userId,
                'titulo' => 'Projeto salvo',
                'data_json' => $request->input('data_json'),
                'width' => $request->input('width'),
                'height' => $request->input('height'),
            ]);
        }

        return response()->json(['success' => true, 'id' => $canvas->id]);
    }

    // Carregar dados do projeto via AJAX para o editor
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

        $dataJson = json_decode($canvas->data_json, true);

        return response()->json([
            'data' => [
                'pages' => $dataJson['pages'] ?? [],
                'activePageIndex' => $dataJson['activePageIndex'] ?? 0,
            ],
            'width' => $canvas->width,
            'height' => $canvas->height,
            'id' => $canvas->id,
            'titulo' => $canvas->titulo,
        ]);
    }

    // Excluir projeto
    public function destroy($id)
    {
        $userId = Auth::id();
        $canvas = CanvasProjeto::where('user_id', $userId)->findOrFail($id);
        $canvas->delete();

        return redirect()->route('canvas.index')->with('success', 'Projeto deletado com sucesso.');
    }
}
