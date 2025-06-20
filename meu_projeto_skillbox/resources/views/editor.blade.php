@extends('layouts.skillboxApp')

@section('title', 'Editor Visual')
@section('titulo', 'Editor Visual')

@section('content')

<script>
    window.initialCanvasWidth = {{ $canvas->width ?? 1000 }};
    window.initialCanvasHeight = {{ $canvas->height ?? 600 }};
</script>


<div class="editor-wrapper">
    <div class="toolbar">
        <button id="add-rect">🟥 Retângulo</button>
        <button id="add-circle">🔵 Círculo</button>
        <button id="add-text">🔤 Texto</button>
        <input type="file" id="upload-image" accept="image/*" />
        <button id="undo" disabled>↩️ Undo</button>
        <button id="redo" disabled>↪️ Redo</button>
        <button id="delete-selected" disabled>❌ Deletar</button>
        <button id="clear-all">🧹 Limpar</button>
        <button id="save-json">💾 Salvar</button>
        <button id="load-json">📂 Carregar</button>
        <button id="export-png">🖼️ PNG</button>
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
        <button id="add-page">➕ Nova Página</button>

        <label>Layout:
            <button id="layout-horizontal">Horizontal</button>
            <button id="layout-vertical">Vertical</button>
        </label>

        <label>
            Largura da Página:
            <input id="page-width" type="number" value="1000" min="100" step="10" />
        </label>

        <label>
            Altura da Página:
            <input id="page-height" type="number" value="600" min="100" step="10" />
        </label>
    </div>

    <div id="pages-container" style="display: flex; gap: 5px; margin-bottom: 10px;">
        <!-- Miniaturas das páginas aparecerão aqui -->
    </div>

    <div class="editor-main">
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

@endsection