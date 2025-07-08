@extends('layouts.skillboxApp')

@section('title', 'Editor Visual')
@section('titulo', 'Editor Visual')

@section('vite')
@vite('resources/js/editor.js')
@endsection

@section('content')

<script>
    window.IMGBB_API_KEY = "{{ $apiKey }}";
    window.initialCanvasWidth = {
        !!json_encode($canvas - > width ?? 1000) !!
    };
    window.initialCanvasHeight = {
        !!json_encode($canvas - > height ?? 600) !!
    };
    window.initialCanvasData = {
        !!$canvas - > data_json ?? 'null'!!
    };
    window.currentCanvasId = {
        {
            $canvas - > id ?? 'null'
        }
    };
</script>

<div class="mb-3">
    <label for="canvas-title" class="form-label">Título do projeto</label>
    <input type="text" class="form-control" id="canvas-title" value="{{ $canvas->titulo ?? '' }}">
</div>

<div class="editor-wrapper" style="display: flex; gap: 20px;">

    <div style="flex: 3;">
        <!-- ... Seu toolbar e editor principal (igual seu código atual) ... -->
        <div class="toolbar">
            <!-- seus botões aqui -->
        </div>
        <div class="page-controls">
            <!-- seus controles de página -->
        </div>
        <div id="pages-container" style="display: flex; gap: 5px; margin-bottom: 10px;">
            <!-- Miniaturas -->
        </div>
        <div class="editor-main">
            <div id="canvas-wrapper">
                <div id="canvas-container"></div>
                <div class="properties" id="properties">
                    <label>🎨 Cor:
                        <input type="color" id="prop-fill" />
                    </label>
                    <label>🔤 Texto:
                        <input type="text" id="prop-text" />
                    </label>
                    <label>📏 Largura:
                        <input type="number" id="prop-width" />
                    </label>
                    <label>📏 Altura:
                        <input type="number" id="prop-height" />
                    </label>
                    <label>🔠 Tamanho da Fonte:
                        <input type="number" id="prop-fontsize" />
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div style="flex: 1;">
        {{-- Abas retráteis para Tarefas e Ferramentas --}}
        <div>
            <button class="toggle-tab btn btn-outline-primary mb-2" data-target="#tasks-tab">Tarefas ({{ $tasks->count() }})</button>
            <!-- Botão para abrir modal adicionar tarefa -->
            <button id="btn-add-task" class="btn btn-sm btn-success mb-2">Adicionar Tarefa</button>

            <div id="tasks-tab" class="tab-content" style="display: none; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                @if ($tasks->isEmpty())
                <p><small>Sem tarefas para este projeto.</small></p>
                @else
                <ul class="list-group">
                    @foreach ($tasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $task->titulo }}</strong><br>
                            <small>{{ $task->descricao }}</small>
                        </div>
                        <span class="badge bg-{{ $task->status === 'concluida' ? 'success' : 'warning text-dark' }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>

        <div>
            <button class="toggle-tab btn btn-outline-secondary mb-2" data-target="#tools-tab">Ferramentas ({{ $tools->count() }})</button>
            <div id="tools-tab" class="tab-content" style="display: none; max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                @if ($tools->isEmpty())
                <p><small>Sem ferramentas disponíveis.</small></p>
                @else
                <ul class="list-group">
                    @foreach ($tools as $tool)
                    <li class="list-group-item">{{ $tool->nome }}</li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal para Adicionar Tarefa -->
<div id="modal-add-task" class="modal" tabindex="-1" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5);">
    <div style="background:#fff; max-width:400px; margin: 10% auto; padding: 20px; border-radius: 8px; position: relative;">
        <h5>Adicionar Tarefa</h5>
        <form action="{{ route('tasks.store', $canvas) }}" method="POST">
            @csrf
            <input type="hidden" name="canvas_projeto_id" value="{{ $canvas->id }}">
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
    // Script simples para togglar as abas retráteis
    document.querySelectorAll('.toggle-tab').forEach(button => {
        button.addEventListener('click', () => {
            const target = document.querySelector(button.dataset.target);
            if (target.style.display === 'none') {
                target.style.display = 'block';
            } else {
                target.style.display = 'none';
            }
        });
    });

    // Modal adicionar tarefa
    const modalTask = document.getElementById('modal-add-task');
    const btnAddTask = document.getElementById('btn-add-task');
    const btnCancelTask = document.getElementById('btn-cancel-task');

    btnAddTask.addEventListener('click', () => {
        modalTask.style.display = 'block';
    });

    btnCancelTask.addEventListener('click', () => {
        modalTask.style.display = 'none';
    });
</script>

@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
@endsection