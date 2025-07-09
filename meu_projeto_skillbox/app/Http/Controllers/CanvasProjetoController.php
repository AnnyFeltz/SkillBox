<?php

namespace App\Http\Controllers;

use App\Models\CanvasImagem;
use Illuminate\Support\Facades\Log;
use App\Models\CanvasProjeto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Tool;
use Illuminate\Support\Facades\Http;


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
            'preview_url' => 'nullable|string',  // aceita o preview_url
        ]);

        $dataJson = $request->input('data_json');
        $previewUrl = $request->input('preview_url'); // pega o preview enviado

        if ($request->id) {
            $canvas = CanvasProjeto::where('user_id', $userId)->findOrFail($request->id);
            $canvas->update([
                'titulo' => $request->input('titulo'),
                'data_json' => $dataJson,
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'preview_url' => $previewUrl,  // atualiza o preview_url
            ]);
        } else {
            $canvas = CanvasProjeto::create([
                'user_id' => $userId,
                'titulo' => $request->input('titulo') ?: 'Projeto salvo',
                'data_json' => $dataJson,
                'width' => $request->input('width'),
                'height' => $request->input('height'),
                'preview_url' => $previewUrl,  // salva o preview_url
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
                'preview_url' => $canvas->preview_url,  // Aqui está a URL da imagem para o preview
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

        if ($tool->user_id !== null && $tool->user_id !== $canvas->user_id && !$tool->is_global) {
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
        $canvas = CanvasProjeto::with(['user', 'tools'])->findOrFail($id);
        return view('canvas.visualizar', compact('canvas'));
    }


    public function publicar(Request $request, $id)
    {
        Log::info("Início do método publicar para canvas ID: {$id}");

        $canvas = CanvasProjeto::findOrFail($id);

        if (Auth::id() !== $canvas->user_id) {
            Log::warning("Usuário " . Auth::id() . " tentou publicar projeto {$id} sem autorização.");
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $imagensData = $request->input('imagens');
        Log::info('Imagens recebidas para upload: ' . json_encode($imagensData));

        if (!is_array($imagensData) || empty($imagensData)) {
            Log::error('Nenhuma imagem válida enviada para publicar.');
            return response()->json(['error' => 'Nenhuma imagem enviada'], 400);
        }

        $IMGBB_API_KEY = config('services.imgbb.key'); // ou env('IMGBB_API_KEY')
        Log::info('Chave ImgBB usada: ' . ($IMGBB_API_KEY ? 'OK' : 'NÃO DEFINIDA'));

        // Apaga imagens antigas
        CanvasImagem::where('canvas_projeto_id', $canvas->id)->delete();
        Log::info("Imagens antigas do projeto {$canvas->id} apagadas.");

        $urlsSalvas = [];

        foreach ($imagensData as $index => $base64png) {
            $base64String = preg_replace('#^data:image/\w+;base64,#i', '', $base64png);

            Log::info("Enviando imagem " . ($index + 1) . " para ImgBB.");

            try {
                $response = Http::asForm()->post("https://api.imgbb.com/1/upload", [
                    'key' => $IMGBB_API_KEY,
                    'image' => $base64String,
                    'name' => 'canvas_' . $canvas->id . '_page_' . ($index + 1),
                ]);
            } catch (\Exception $e) {
                Log::error("Erro na requisição para ImgBB: " . $e->getMessage());
                return response()->json(['error' => 'Erro ao conectar com ImgBB'], 500);
            }

            Log::info('Resposta da API ImgBB: ' . $response->body());

            if ($response->successful() && $response->json('success')) {
                $url = $response->json('data.url');
                CanvasImagem::create([
                    'canvas_projeto_id' => $canvas->id,
                    'url' => $url,
                    'pagina' => $index + 1,
                ]);
                $urlsSalvas[] = $url;
                Log::info("Imagem " . ($index + 1) . " enviada com sucesso: {$url}");
            } else {
                Log::error("Falha ao enviar imagem " . ($index + 1) . " para ImgBB: " . $response->body());
                return response()->json(['error' => 'Falha ao enviar imagem para ImgBB'], 500);
            }
        }

        // Atualiza preview e status público
        $canvas->preview_url = $urlsSalvas[0] ?? null;
        $canvas->is_public = true;
        $canvas->save();

        Log::info("Projeto {$canvas->id} publicado com sucesso. Preview URL: {$canvas->preview_url}");

        return response()->json([
            'success' => true,
            'urls' => $urlsSalvas,
            'message' => 'Projeto publicado com sucesso!'
        ]);
    }

    public function publicados()
    {
        $publicados = CanvasProjeto::where('is_public', true)
            ->with('user')
            ->latest()
            ->paginate(8);

        return view('canvas.publicados', compact('publicados'));
    }


    public function galeria()
    {
        $users = \App\Models\User::with(['canvasProjetos' => function ($q) {
            $q->latest();
        }])->get();

        return view('canvas.galeria', compact('users'));
    }
}
