// resources/js/editor.js

import Konva from 'konva';

document.addEventListener('DOMContentLoaded', () => {
    const canvasContainer = document.getElementById('canvas-container');

    // Estado global
    let pages = [];
    let activePageIndex = 0;
    let layout = 'horizontal';
    let pageWidth = window.initialCanvasWidth || 1000;
    let pageHeight = window.initialCanvasHeight || 600;

    let stage = new Konva.Stage({
        container: 'canvas-container',
        width: pageWidth,
        height: pageHeight,
        draggable: true,
    });

    let layer = new Konva.Layer();
    stage.add(layer);

    const paperRect = new Konva.Rect({
        x: 20,
        y: 20,
        width: pageWidth - 40,
        height: pageHeight - 40,
        stroke: '#555',
        strokeWidth: 2,
        dash: [10, 5],
        fill: '#fff',
        listening: false,
    });

    layer.add(paperRect);
    paperRect.moveToBottom();

    let selectedShape = null;
    let tr = new Konva.Transformer();
    layer.add(tr);

    function updatePropertiesPanel(shape) {
        const panel = document.getElementById('properties');
        if (!shape) {
            panel.classList.remove('active');
            return;
        }
        panel.classList.add('active');

        document.getElementById('prop-fill').value = shape.fill() || '#000000';
        document.getElementById('prop-text').value = shape.text ? shape.text() : '';
        document.getElementById('prop-width').value = shape.width ? shape.width() : '';
        document.getElementById('prop-height').value = shape.height ? shape.height() : '';
        document.getElementById('prop-fontsize').value = shape.fontSize ? shape.fontSize() : '';
    }

    function deselect() {
        selectedShape = null;
        tr.nodes([]);
        updatePropertiesPanel(null);
        document.getElementById('delete-selected').disabled = true;
    }

    layer.on('click', (e) => {
        if (e.target === stage) {
            deselect();
            layer.draw();
            return;
        }
        selectedShape = e.target;
        tr.nodes([selectedShape]);
        updatePropertiesPanel(selectedShape);
        document.getElementById('delete-selected').disabled = false;
        layer.draw();
    });

    const history = [];
    let historyIndex = -1;

    function saveHistory() {
        history.splice(historyIndex + 1);
        history.push(JSON.stringify({ pages, activePageIndex }));
        historyIndex++;
        updateUndoRedoButtons();
    }

    function undo() {
        if (historyIndex <= 0) return;
        historyIndex--;
        loadFromHistory(history[historyIndex]);
        updateUndoRedoButtons();
    }

    function redo() {
        if (historyIndex >= history.length - 1) return;
        historyIndex++;
        loadFromHistory(history[historyIndex]);
        updateUndoRedoButtons();
    }

    function loadFromHistory(json) {
        const state = JSON.parse(json);
        pages = state.pages;
        activePageIndex = state.activePageIndex;
        renderPageThumbnails();
        loadPage(activePageIndex);
    }

    function updateUndoRedoButtons() {
        document.getElementById('undo').disabled = historyIndex <= 0;
        document.getElementById('redo').disabled = historyIndex >= history.length - 1;
    }

    function createNewPage() {
        const newPage = {
            id: pages.length + 1,
            width: pageWidth,
            height: pageHeight,
            shapes: [],
        };
        pages.push(newPage);
        activePageIndex = pages.length - 1;
        renderPageThumbnails();
        loadPage(activePageIndex);
        saveHistory();
    }

    function loadPage(index) {
        if (index < 0 || index >= pages.length) return;
        const page = pages[index];
        activePageIndex = index;
        stage.width(page.width);
        stage.height(page.height);
        canvasContainer.style.width = page.width + 'px';
        canvasContainer.style.height = page.height + 'px';
        paperRect.width(page.width - 40);
        paperRect.height(page.height - 40);
        layer.destroyChildren();
        layer.add(paperRect);
        paperRect.moveToBottom();
        page.shapes.forEach(shapeJSON => {
            const shape = Konva.Node.create(shapeJSON);
            layer.add(shape);
        });
        deselect();
        layer.draw();
        renderPageThumbnails();
    }

    function saveCurrentPageShapes() {
        const shapes = layer.getChildren(shape => {
            return shape !== paperRect && !(shape instanceof Konva.Transformer);
        });
        pages[activePageIndex].shapes = shapes.map(s => s.toJSON());
    }

    function renderPageThumbnails() {
        const container = document.getElementById('pages-container');
        if (!container) return;
        container.innerHTML = '';
        container.className = layout;

        pages.forEach((page, idx) => {
            const div = document.createElement('div');
            div.textContent = 'Página ' + page.id;
            div.style.border = idx === activePageIndex ? '2px solid blue' : '1px solid gray';
            div.style.padding = '5px';
            div.style.cursor = 'pointer';
            div.style.margin = '5px';
            div.style.userSelect = 'none';
            div.onclick = () => {
                saveCurrentPageShapes();
                loadPage(idx);
                saveHistory();
            };
            container.appendChild(div);
        });
    }

    function updatePageSizes(newWidth, newHeight) {
        pageWidth = newWidth;
        pageHeight = newHeight;
        pages.forEach(page => {
            page.width = newWidth;
            page.height = newHeight;
        });
        loadPage(activePageIndex);
        saveHistory();
    }

    document.getElementById('layout-horizontal').onclick = () => {
        layout = 'horizontal';
        renderPageThumbnails();
    };
    document.getElementById('layout-vertical').onclick = () => {
        layout = 'vertical';
        renderPageThumbnails();
    };

    document.getElementById('page-width').onchange = (e) => {
        const val = parseInt(e.target.value);
        if (val > 100) updatePageSizes(val, pageHeight);
    };

    document.getElementById('page-height').onchange = (e) => {
        const val = parseInt(e.target.value);
        if (val > 100) updatePageSizes(pageWidth, val);
    };

    document.getElementById('add-page').onclick = () => {
        saveCurrentPageShapes();
        createNewPage();
    };

    // Os outros handlers continuam como no seu script original...

    function init() {
        if (pages.length === 0) {
            createNewPage();
        } else {
            renderPageThumbnails();
            loadPage(activePageIndex);
        }
    }

    init();
});
