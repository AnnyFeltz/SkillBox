import Konva from 'konva';

document.addEventListener('DOMContentLoaded', () => {
    // Variáveis iniciais do Blade
    let pages = window.initialCanvasData?.pages || [];
    let activePageIndex = window.initialCanvasData?.activePageIndex || 0;
    let pageWidth = window.initialCanvasWidth || 1000;
    let pageHeight = window.initialCanvasHeight || 600;

    const canvasContainer = document.getElementById('canvas-container');

    // Alinha o container no centro da tela
    canvasContainer.style.display = 'flex';
    canvasContainer.style.justifyContent = 'center';
    canvasContainer.style.alignItems = 'center';
    canvasContainer.style.margin = '0 auto';
    canvasContainer.style.width = `${pageWidth}px`;
    canvasContainer.style.height = `${pageHeight}px`;

    // Stage e layer
    let stage = new Konva.Stage({
        container: 'canvas-container',
        width: pageWidth,
        height: pageHeight,
        draggable: true,
    });

    let layer = new Konva.Layer();
    stage.add(layer);

    // Papel branco como fundo
    const paperRect = new Konva.Rect({
        x: 0,
        y: 0,
        width: pageWidth,
        height: pageHeight,
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

    function deleteCurrentPage() {
        if (pages.length <= 1) {
            alert('Não é possível deletar a última página.');
            return;
        }
        pages.splice(activePageIndex, 1);
        if (activePageIndex >= pages.length) {
            activePageIndex = pages.length - 1;
        }
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

        paperRect.width(page.width);
        paperRect.height(page.height);

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
        container.className = 'horizontal';

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

    function addRectangle() {
        const rect = new Konva.Rect({
            x: 50,
            y: 50,
            width: 100,
            height: 60,
            fill: 'red',
            stroke: 'black',
            strokeWidth: 1,
            draggable: true,
        });
        layer.add(rect);
        layer.draw();
        saveCurrentPageShapes();
        saveHistory();
    }

    function addCircle() {
        const circle = new Konva.Circle({
            x: 150,
            y: 150,
            radius: 50,
            fill: 'blue',
            stroke: 'black',
            strokeWidth: 1,
            draggable: true,
        });
        layer.add(circle);
        layer.draw();
        saveCurrentPageShapes();
        saveHistory();
    }

    function addText() {
        const text = new Konva.Text({
            x: 100,
            y: 100,
            text: 'Texto',
            fontSize: 24,
            fontFamily: 'Arial',
            fill: 'black',
            draggable: true,
        });
        layer.add(text);
        layer.draw();
        saveCurrentPageShapes();
        saveHistory();
    }

    function uploadImage(file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const imageObj = new Image();
            imageObj.onload = function () {
                const konvaImage = new Konva.Image({
                    x: 50,
                    y: 50,
                    image: imageObj,
                    width: imageObj.width / 2,
                    height: imageObj.height / 2,
                    draggable: true,
                });
                layer.add(konvaImage);
                layer.draw();
                saveCurrentPageShapes();
                saveHistory();
            };
            imageObj.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    document.getElementById('add-rect').addEventListener('click', () => addRectangle());
    document.getElementById('add-circle').addEventListener('click', () => addCircle());
    document.getElementById('add-text').addEventListener('click', () => addText());
    document.getElementById('upload-image').addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            uploadImage(e.target.files[0]);
            e.target.value = '';
        }
    });
    document.getElementById('undo').addEventListener('click', () => undo());
    document.getElementById('redo').addEventListener('click', () => redo());
    document.getElementById('delete-selected').addEventListener('click', () => {
        if (selectedShape) {
            selectedShape.destroy();
            deselect();
            layer.draw();
            saveCurrentPageShapes();
            saveHistory();
        }
    });
    document.getElementById('clear-all').addEventListener('click', () => {
        layer.destroyChildren();
        layer.add(paperRect);
        paperRect.moveToBottom();
        deselect();
        layer.draw();
        pages[activePageIndex].shapes = [];
        saveHistory();
    });
    document.getElementById('save-json').addEventListener('click', () => {
        saveCurrentPageShapes();
        const data = JSON.stringify({ pages, activePageIndex });
        const blob = new Blob([data], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'canvas-data.json';
        document.body.appendChild(a);
        a.click();
        a.remove();
        URL.revokeObjectURL(url);
    });
    document.getElementById('load-json').addEventListener('click', () => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.json,application/json';
        input.onchange = (e) => {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (ev) => {
                try {
                    const loadedData = JSON.parse(ev.target.result);
                    if (loadedData.pages && Array.isArray(loadedData.pages)) {
                        pages = loadedData.pages;
                        activePageIndex = loadedData.activePageIndex || 0;
                        loadPage(activePageIndex);
                        saveHistory();
                    } else {
                        alert('Arquivo JSON inválido.');
                    }
                } catch {
                    alert('Erro ao ler arquivo JSON.');
                }
            };
            reader.readAsText(file);
        };
        input.click();
    });
    document.getElementById('export-png').addEventListener('click', () => {
        saveCurrentPageShapes();
        stage.toDataURL({
            mimeType: 'image/png',
            callback: function (dataUrl) {
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = `canvas-page-${activePageIndex + 1}.png`;
                document.body.appendChild(a);
                a.click();
                a.remove();
            }
        });
    });
    document.getElementById('edit-size').addEventListener('click', () => {
        const pageSizesDiv = document.querySelector('.page-sizes');
        pageSizesDiv.style.display = (pageSizesDiv.style.display === 'none' || !pageSizesDiv.style.display) ? 'block' : 'none';
    });
    document.getElementById('page-width').addEventListener('change', (e) => {
        const val = parseInt(e.target.value);
        if (val >= 100) updatePageSizes(val, pageHeight);
    });
    document.getElementById('page-height').addEventListener('change', (e) => {
        const val = parseInt(e.target.value);
        if (val >= 100) updatePageSizes(pageWidth, val);
    });
    document.getElementById('add-page').addEventListener('click', () => {
        saveCurrentPageShapes();
        createNewPage();
    });
    document.getElementById('delete-page').addEventListener('click', () => deleteCurrentPage());

    document.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
        if (e.key === 'ArrowRight' || e.key === '>') {
            if (activePageIndex < pages.length - 1) {
                saveCurrentPageShapes();
                loadPage(activePageIndex + 1);
                saveHistory();
            }
        } else if (e.key === 'ArrowLeft' || e.key === '<') {
            if (activePageIndex > 0) {
                saveCurrentPageShapes();
                loadPage(activePageIndex - 1);
                saveHistory();
            }
        }
    });

    function init() {
        if (pages.length === 0) {
            createNewPage();
        } else {
            renderPageThumbnails();
            loadPage(activePageIndex);
        }
        saveHistory();
    }

    init();
});
