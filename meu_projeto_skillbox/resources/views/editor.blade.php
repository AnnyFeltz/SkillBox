@extends('layouts.skillboxApp')

@section('title', 'Editor Visual')
@section('titulo', 'Editor Visual')

@section('content')

<script>
    window.initialCanvasWidth = {{ json_encode($canvas->width ?? 1000) }};
    window.initialCanvasHeight = {{ json_encode($canvas->height ?? 600) }};
    window.initialCanvasData = {!! isset($canvas->data_json) ? $canvas->data_json : 'null' !!};
</script>

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
        <label for="zoom-range">🔍 Zoom</label>
        <input
            type="range"
            id="zoom-range"
            min="0.5"
            max="2"
            step="0.1"
            value="1" />
    </div>

    <div class="page-controls" style="margin: 10px 0;">
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

    <div class="editor-main" style="display: flex; gap: 20px;">
        <div id="canvas-container" style="border: 1px solid #ccc; background: #eee;"></div>

        <div class="properties" id="properties" style="min-width: 200px; display: none; flex-shrink: 0;">
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

@endsection
