<?php

namespace App\Http\Controllers;

use App\Models\Canvas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanvasController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'data_json' => 'required|string',
        ]);

        $canvas = Canvas::create([
            'title' => $data['title'] ?? 'Sem título',
            'data_json' => $data['data_json'],
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Canvas salvo com sucesso!', 'id' => $canvas->id]);
    }

    public function show($id)
    {
        $canvas = Canvas::findOrFail($id);
        return response()->json($canvas);
    }

    public function index()
    {
        $canvases = Canvas::where('user_id', Auth::id())->latest()->get();
        return view('canvas.index', compact('canvases'));
    }


    public function destroy($id)
    {
        $canvas = Canvas::findOrFail($id);

        if ($canvas->user_id !== Auth::id()) {
            abort(403);
        }

        $canvas->delete();

        return redirect()->route('canvas.index')->with('success', 'Canvas deletado!');
    }

    public function editor(Request $request)
    {
        $id = $request->query('id');
        $new = $request->query('new');
        $width = (int) $request->query('width', 1000);
        $height = (int) $request->query('height', 600);

        if ($new) {
            // Criar novo canvas
            $canvas = Canvas::create([
                'title' => 'Canvas novo',
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

            return redirect("/editor?id={$canvas->id}");
        }

        if ($id) {
            $canvas = Canvas::where('user_id', Auth::id())->findOrFail($id);
        } else {
            abort(404, 'Canvas não encontrado');
        }

        return view('editor', compact('canvas'));
    }
}
