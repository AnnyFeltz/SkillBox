@extends('layouts.skillboxApp')

@section('title', 'Editor Visual')
@section('titulo', 'Editor Visual')

@section('vite')
@vite('resources/js/editor.js')
@endsection

@section('content')

<script>
    window.IMGBB_API_KEY = "{{ $apiKey }}";
    window.initialCanvasWidth = {!! json_encode($canvas->width ?? 1000) !!};
    window.initialCanvasHeight = {!! json_encode($canvas->height ?? 600) !!};
    window.initialCanvasData = {!! $canvas->data_json ?? 'null' !!};
    window.currentCanvasId = {{ $canvas->id ?? 'null' }};
</script>

<div class="mb-3">
    <label for="canvas-title" class="form-label">Título do projeto</label>
    <input type="text" class="form-control" id="canvas-title" value="{{ $canvas->titulo ?? '' }}">
</div>

<div class="editor-wrapper">
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
        <input
            type="range"
            id="zoom-range"
            min="0.5"
            max="2"
            step="0.1"
            value="1" />
        <label for="zoom-range" class="material-icons">zoom_in</label>
        <button id="reset-zoom" class="material-icons">reset_focus</button>
    </div>

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

    <div id="pages-container" style="display: flex; gap: 5px; margin-bottom: 10px;">
        <!-- Miniaturas das páginas aparecerão aqui -->
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

@endsection

@section('menu-items')
<li><a href="/dashboard"><i class="icon-dashboard material-symbols-outlined">dashboard</i> <span class="label">Dashboard</span></a></li>
<li><a href="/profile"><i class="icon-user material-symbols-outlined">account_circle</i> <span class="label">Perfil</span></a></li>
<li><a href="/canvas"><i class="icon-user material-symbols-outlined">wall_art</i> <span class="label">Canvas</span></a></li>
@endsection