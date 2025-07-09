@extends('layouts.skillboxApp')

@section('title', 'Editor Visual')
@section('titulo', '🎨Área de Edição')

@section('vite')
@vite('resources/js/editor.js')
@endsection

@section('content')

<script>
    window.IMGBB_API_KEY = "{{ $apiKey }}";
    window.initialCanvasWidth = {!! json_encode($canvas->width ?? 1000) !!};
    window.initialCanvasHeight = {!! json_encode($canvas->height ?? 600) !!};
    window.initialCanvasData = {!! json_encode($canvas->data_json ?? null) !!};
    window.currentCanvasId = {{ $canvas->id ?? 'null' }};
</script>

<div class="mb-3">
    <label for="canvas-title" class="form-label">Título do projeto</label>
    <input type="text" class="form-control" id="canvas-title" value="{{ $canvas->titulo ?? '' }}">
</div>

<div class="editor-wrapper" style="display: flex; gap: 20px;">
    <div style="flex: 3;">
        {{-- Toolbar restaurada --}}
        <div class="toolbar">
            <button id="add-rect" class="material-icons" title="Retângulo">rectangle</button>
            <button id="add-circle" class="material-icons" title="Círculo">circle</button>
            <button id="add-text" class="material-icons" title="Texto">text_fields</button>
            <input type="file" id="upload-image" accept="image/*" />
            <button id="undo" class="material-icons" disabled title="Desfazer">undo</button>
            <button id="redo" class="material-icons" disabled title="Refazer">redo</button>
            <button id="delete-selected" class="material-icons" disabled title="Deletar Selecionado">delete</button>
            <button id="clear-all" class="material-icons" title="Limpar Tudo">clear_all</button>
            <button id="save-json" class="material-icons" title="Salvar">save</button>
            <button id="load-json" class="material-icons" title="Carregar">folder_open</button>
            <button id="export-png" class="material-icons" title="Exportar PNG">image</button>
            <label for="zoom-range" class="material-icons">zoom_out</label>
            <input type="range" id="zoom-range" min="0.5" max="2" step="0.1" value="1" />
            <label for="zoom-range" class="material-icons">zoom_in</label>
            <button id="reset-zoom" class="material-icons">reset_focus</button>
            <button id="publish" class="material-icons">publish</button>

        </div>

        {{-- Controles de página --}}
        <div class="page-controls">
            <button id="add-page" class="material-icons" title="Nova Página">add_box</button>
            <button id="edit-size" class="material-icons" title="Editar Tamanho">resize</button>
            <button id="delete-page" class="material-icons" title="Excluir Página" style="margin-left: 10px;">delete_forever</button>

            <div class="page-sizes" style="display: none; margin-top: 10px;">
                <label>
                    Largura da Página:
                    <input id="page-width" type="number" value="{{ $canvas->width ?? 1000 }}" min="100" step="10" />
                </label>

                <label style="margin-left: 10px;">
                    Altura da Página:
                    <input id="page-height" type="number" value="{{ $canvas->height ?? 600 }}" min="100" step="10" />
                </label>
            </div>
        </div>

        <div id="pages-container" style="display: flex; gap: 5px; margin-bottom: 10px;"></div>

        <div class="editor-main">
            <div id="canvas-wrapper">
                <div id="canvas-container"></div>
                <div class="properties" id="properties">
                    <label>🎨 Cor: <input type="color" id="prop-fill" /></label>
                    <label>🔤 Texto: <input type="text" id="prop-text" /></label>
                    <label>📏 Largura: <input type="number" id="prop-width" /></label>
                    <label>📏 Altura: <input type="number" id="prop-height" /></label>
                    <label>🔠 Tamanho da Fonte: <input type="number" id="prop-fontsize" /></label>
                </div>
            </div>
        </div>
    </div>

    {{-- Lado direito: tarefas e ferramentas --}}
    <div style="flex: 1;">
        {{-- Tarefas --}}
        <div>
            <button class="toggle-tab btn btn-outline-primary mb-2" data-target="#tasks-tab">
                Tarefas ({{ $tasks->count() }})
            </button>
            <button id="btn-add-task" class="btn btn-sm btn-success mb-2">Adicionar Tarefa</button>

            <div id="tasks-tab" class="tab-content" style="display: none; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                @if ($tasks->isEmpty())
                <p><small>Sem tarefas para este projeto.</small></p>
                @else
                <ul class="list-group">
                    @foreach ($tasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div style="word-break: break-word; max-width: 800px;">
                            <strong>{{ $task->titulo }}</strong><br>
                            <small>{{ $task->descricao }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form action="{{ route('tasks.toggle', ['canvas' => $canvas->id, 'task' => $task->id]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-{{ $task->status === 'concluida' ? 'warning' : 'success' }}" title="Alterar status">
                                    {{ $task->status === 'concluida' ? 'Marcar como Pendente' : 'Concluir' }}
                                </button>
                            </form>
                            <span class="badge bg-{{ $task->status === 'concluida' ? 'success' : 'warning text-dark' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
                <a href="{{ route('tasks.index', ['canvas' => $canvas->id]) }}" class="btn btn-outline-secondary mt-2">📋 Visualizar todas as tarefas</a>
            </div>
        </div>

        {{-- Ferramentas --}}
        {{-- Ferramentas --}}
        <div>
            <button class="toggle-tab btn btn-outline-secondary mb-2" data-target="#tools-tab">
                Ferramentas ({{ $tools->count() }})
            </button>

            <div id="tools-tab" class="tab-content" style="display: none; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                {{-- Formulário para associar ferramenta existente --}}
                <form action="{{ route('canvas.tools.adicionar', $canvas->id) }}" method="POST" class="mb-2">
                    @csrf
                    <div class="input-group">
                        <select name="tool_id" class="form-select form-select-sm" required>
                            <option value="">Escolher ferramenta</option>
                            @php
                            $disponiveis = \App\Models\Tool::where(function ($query) {
                            $query->where('is_global', true)
                            ->orWhere('user_id', Auth::id());
                            })->get()->filter(function ($tool) use ($canvas) {
                            return !$canvas->tools->contains($tool->id);
                            });
                            @endphp

                            @foreach ($disponiveis as $tool)
                            <option value="{{ $tool->id }}">{{ $tool->nome }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-sm btn-outline-success" type="submit">+ Associar</button>
                    </div>
                </form>

                <a href="{{ route('tools.create') }}" class="btn btn-sm btn-primary mb-2">Criar Nova Ferramenta</a>
                <a href="{{ route('tools.index') }}" class="btn btn-outline-secondary mb-2">🔧 Visualizar todas</a>

                @if ($tools->isEmpty())
                <p><small>Sem ferramentas associadas a este projeto.</small></p>
                @else
                <ul class="list-group">
                    @foreach ($tools as $tool)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $tool->nome }}
                        <form action="{{ route('canvas.tools.remover', ['canvas' => $canvas->id, 'tool' => $tool->id]) }}" method="POST" onsubmit="return confirm('Remover esta ferramenta do projeto?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Remover">✖</button>
                        </form>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="modal-add-task" class="modal" tabindex="-1" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5);">
    <div style="background:#fff; max-width:400px; margin: 10% auto; padding: 20px; border-radius: 8px; position: relative;">
        <h5>Adicionar Tarefa</h5>
        <form action="{{ route('tasks.store', $canvas) }}" method="POST">
            @csrf
            <input type="hidden" name="canvas_projeto_id" value="{{ $canvas->id }}">
            <input type="hidden" name="from_editor" value="1">
            <div class="mb-3">
                <label for="task-titulo" class="form-label">Título</label>
                <input type="text" id="task-titulo" name="titulo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="task-descricao" class="form-label">Descrição</label>
                <textarea id="task-descricao" name="descricao" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Adicionar</button>
            <button type="button" class="btn btn-secondary" id="btn-cancel-task">Cancelar</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-tab').forEach(button => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.target);
            target.style.display = target.style.display === 'none' ? 'block' : 'none';
        });
    });

    const modalTask = document.getElementById('modal-add-task');
    const btnAddTask = document.getElementById('btn-add-task');
    const btnCancelTask = document.getElementById('btn-cancel-task');

    btnAddTask.addEventListener('click', () => modalTask.style.display = 'block');
    btnCancelTask.addEventListener('click', () => modalTask.style.display = 'none');
</script>

@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
@endsection