import Konva from 'konva';

console.log('Konva importado:', Konva);
console.log('Elementos no DOM:', document.getElementById('canvas-container'));
console.log('Dimensões iniciais:', window.initialCanvasWidth, window.initialCanvasHeight);

document.addEventListener('DOMContentLoaded', () => {
    let pages = [];
    let activePageIndex = 0;
    let pageWidth = window.initialCanvasWidth || 1000;
    let pageHeight = window.initialCanvasHeight || 600;
    let history = [];
    let historyStep = -1;

    const maxHistory = 50; // limita o número de estados guardados pra economizar memória
    const canvasContainer = document.getElementById('canvas-container');
    const wrapper = document.getElementById('canvas-wrapper');
    if (wrapper) {
        wrapper.style.display = 'flex';
        wrapper.style.justifyContent = 'center';
        wrapper.style.alignItems = 'center';
        wrapper.style.width = '100vw';
        wrapper.style.height = '100vh';
        wrapper.style.overflow = 'hidden';
    }

    canvasContainer.style.backgroundColor = '#eee';
    canvasContainer.style.margin = 'auto';
    canvasContainer.style.overflow = 'hidden';

    let stage = new Konva.Stage({
        container: 'canvas-container',
        width: pageWidth,
        height: pageHeight,
        draggable: true,
        dragBoundFunc: function (pos) {
            const scale = stage.scaleX();
            const widthScaled = stage.width() * scale;
            const heightScaled = stage.height() * scale;

            if (scale <= 1) {
                return pos;
            } else {
                const minX = stage.width() - widthScaled;
                const minY = stage.height() - heightScaled;
                const newX = Math.min(0, Math.max(pos.x, minX));
                const newY = Math.min(0, Math.max(pos.y, minY));
                return { x: newX, y: newY };
            }
        }
    });

    let layer = new Konva.Layer();
    stage.add(layer);

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
    let tr = new Konva.Transformer({
        rotateEnabled: true,
        ignoreStroke: true,
        padding: 5,
    });
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

    stage.on('click', (e) => {
        const target = e.target;

        if (target === stage || target === paperRect) {
            deselect();
            layer.draw();
            return;
        }

        if (target instanceof Konva.Shape && target !== tr && target !== paperRect) {
            selectedShape = target;
            tr.nodes([selectedShape]);
            layer.draw();

            updatePropertiesPanel(selectedShape);
            document.getElementById('delete-selected').disabled = false;
        } else {
            deselect();
            layer.draw();
        }

    });

    function saveHistory() {
        if (historyStep < history.length - 1) {
            history = history.slice(0, historyStep + 1);
        }
        const currentState = JSON.stringify(pages);
        history.push(currentState);

        if (history.length > maxHistory) {
            history.shift();
        } else {
            historyStep++;
        }

        updateUndoRedoButtons();
    }

    function undo() {
        if (historyStep > 0) {
            historyStep--;
            restoreHistory(history[historyStep]);
        }
        updateUndoRedoButtons();
    }

    function redo() {
        if (historyStep < history.length - 1) {
            historyStep++;
            restoreHistory(history[historyStep]);
        }
        updateUndoRedoButtons();
    }

    function updateUndoRedoButtons() {
        document.getElementById('undo').disabled = historyStep <= 0;
        document.getElementById('redo').disabled = historyStep >= history.length - 1;
    }


    function restoreHistory(stateJson) {
        try {
            const state = JSON.parse(stateJson);
            pages = state;
            activePageIndex = Math.min(activePageIndex, pages.length - 1);
            loadPage(activePageIndex);
            renderPageThumbnails();
        } catch (e) {
            console.error('Erro ao restaurar estado do histórico:', e);
        }
    }


    function updateAndSave() {
        layer.batchDraw();
        saveCurrentPageShapes();
        salvarNoServidor();
    }

    document.getElementById('undo').addEventListener('click', () => {
        undo();
    });

    document.getElementById('redo').addEventListener('click', () => {
        redo();
    });


    document.getElementById('prop-fill').addEventListener('input', (e) => {
        if (selectedShape && selectedShape.fill) {
            selectedShape.fill(e.target.value);
            updateAndSave();
        }
    });

    document.getElementById('prop-text').addEventListener('input', (e) => {
        if (selectedShape && selectedShape.text) {
            selectedShape.text(e.target.value);
            updateAndSave();
        }
    });

    document.getElementById('prop-width').addEventListener('input', (e) => {
        if (selectedShape && selectedShape.width) {
            let val = parseInt(e.target.value);
            if (val > 0) {
                selectedShape.width(val);
                updateAndSave();
            }
        }
    });

    document.getElementById('prop-height').addEventListener('input', (e) => {
        if (selectedShape && selectedShape.height) {
            let val = parseInt(e.target.value);
            if (val > 0) {
                selectedShape.height(val);
                updateAndSave();
            }
        }
    });

    document.getElementById('prop-fontsize').addEventListener('input', (e) => {
        if (selectedShape && selectedShape.fontSize) {
            let val = parseInt(e.target.value);
            if (val > 0) {
                selectedShape.fontSize(val);
                updateAndSave();
            }
        }
    });

    const zoomRange = document.getElementById('zoom-range');
    if (zoomRange) {
        zoomRange.addEventListener('input', (e) => {
            const scale = parseFloat(e.target.value);
            const oldScale = stage.scaleX();
            const pointer = stage.getPointerPosition() || { x: stage.width() / 2, y: stage.height() / 2 };

            const mousePointTo = {
                x: (pointer.x - stage.x()) / oldScale,
                y: (pointer.y - stage.y()) / oldScale,
            };

            stage.scale({ x: scale, y: scale });

            const newPos = {
                x: pointer.x - mousePointTo.x * scale,
                y: pointer.y - mousePointTo.y * scale,
            };

            stage.position(newPos);
            stage.batchDraw();
        });
    }

    canvasContainer.addEventListener('wheel', (e) => {
        e.preventDefault();
        if (!e.ctrlKey) return;

        const scaleBy = 1.05;
        const oldScale = stage.scaleX();
        const pointer = stage.getPointerPosition();

        if (!pointer) return;

        const mousePointTo = {
            x: (pointer.x - stage.x()) / oldScale,
            y: (pointer.y - stage.y()) / oldScale,
        };

        let newScale = e.deltaY < 0 ? oldScale * scaleBy : oldScale / scaleBy;
        newScale = Math.max(0.5, Math.min(newScale, 5));

        stage.scale({ x: newScale, y: newScale });
        const newPos = {
            x: pointer.x - mousePointTo.x * newScale,
            y: pointer.y - mousePointTo.y * newScale,
        };

        stage.position(newPos);
        stage.batchDraw();
        if (zoomRange) zoomRange.value = newScale.toFixed(2);
    });

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
        salvarNoServidor();
    }

    function deleteCurrentPage() {
        if (pages.length <= 1) {
            alert('Não é possível deletar a última página.');
            return;
        }
        pages.splice(activePageIndex, 1);
        if (activePageIndex >= pages.length) activePageIndex = pages.length - 1;
        renderPageThumbnails();
        loadPage(activePageIndex);
        salvarNoServidor();
    }

    function loadPage(index) {
        if (index < 0 || index >= pages.length) return;
        const page = pages[index];
        activePageIndex = index;

        pageWidth = page.width;
        pageHeight = page.height;

        stage.width(pageWidth);
        stage.height(pageHeight);
        canvasContainer.style.width = pageWidth + 'px';
        canvasContainer.style.height = pageHeight + 'px';

        paperRect.width(pageWidth);
        paperRect.height(pageHeight);

        layer.destroyChildren();

        layer.add(paperRect);
        layer.add(tr);
        paperRect.moveToBottom();

        const shapesToLoad = page.shapes || [];
        let imagesToLoad = 0;
        let imagesLoaded = 0;

        function checkAllImagesLoaded() {
            if (imagesLoaded >= imagesToLoad) {
                deselect();
                layer.draw();
                renderPageThumbnails();
            }
        }

        shapesToLoad.forEach(shapeJSON => {
            if (shapeJSON.className === 'Image' && shapeJSON.attrs.imageBase64) {
                imagesToLoad++;
                const imageObj = new Image();
                imageObj.onload = () => {
                    const konvaImage = new Konva.Image({
                        ...shapeJSON.attrs,
                        image: imageObj,
                    });
                    layer.add(konvaImage);

                    konvaImage.on('click', () => {
                        selectedShape = konvaImage;
                        tr.nodes([]);
                        tr.nodes([selectedShape]);
                        console.log('Selecionado:', selectedShape);
                        console.log('Transformer nodes:', tr.nodes());
                        updatePropertiesPanel(selectedShape);
                        document.getElementById('delete-selected').disabled = false;
                        layer.draw();
                    });

                    imagesLoaded++;
                    checkAllImagesLoaded();
                };
                imageObj.src = shapeJSON.attrs.imageBase64;
            } else {
                const shape = Konva.Node.create(shapeJSON);
                layer.add(shape);

                shape.on('click', (e) => {
                    selectedShape = shape;
                    tr.nodes([]);
                    tr.nodes([selectedShape]);
                    console.log('Selecionado:', selectedShape);
                    console.log('Transformer nodes:', tr.nodes());
                    updatePropertiesPanel(selectedShape);
                    document.getElementById('delete-selected').disabled = false;
                    layer.draw();
                });
            }
        });

        if (imagesToLoad === 0) {
            deselect();
            layer.draw();
            renderPageThumbnails();
        }

        if (selectedShape && selectedShape.getParent()) {
            tr.nodes([selectedShape]);
        } else {
            deselect();
        }

        layer.draw();
    }

    function saveCurrentPageShapes() {
        pages[activePageIndex].shapes = getShapesJsonWithImages();
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
                salvarNoServidor();
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
        salvarNoServidor();
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
        salvarNoServidor();
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
        salvarNoServidor();
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
        salvarNoServidor();
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
                salvarNoServidor();
            };
            imageObj.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    document.getElementById('add-rect').addEventListener('click', addRectangle);
    document.getElementById('add-circle').addEventListener('click', addCircle);
    document.getElementById('add-text').addEventListener('click', addText);
    document.getElementById('upload-image').addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            uploadImage(e.target.files[0]);
            e.target.value = '';
        }
    });

    function safeDestroy(shape) {
        if (!shape) return;
        if (shape._isDragging) {
            // Se estiver sendo arrastada, tenta de novo depois de 50ms
            setTimeout(() => safeDestroy(shape), 50);
        } else {
            shape.destroy();
            deselect();
            layer.draw();
            saveCurrentPageShapes();
            salvarNoServidor();
        }
    }

    document.getElementById('delete-selected').addEventListener('click', () => {
        if (selectedShape) {
            tr.nodes([]);
            safeDestroy(selectedShape);
        }
    });

    document.getElementById('clear-all').addEventListener('click', () => {
        layer.destroyChildren();
        layer.add(paperRect);
        paperRect.moveToBottom();
        deselect();
        layer.draw();
        pages[activePageIndex].shapes = [];
        salvarNoServidor();
    });

    document.getElementById('save-json').addEventListener('click', () => {
        saveCurrentPageShapes();
        salvarNoServidor(true);
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
                        salvarNoServidor();
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

        const oldScale = stage.scaleX();
        const oldPos = stage.position();

        stage.scale({ x: 1, y: 1 });
        stage.position({ x: 0, y: 0 });
        stage.batchDraw();

        stage.toDataURL({
            mimeType: 'image/png',
            callback: function (dataUrl) {
                const a = document.createElement('a');
                a.href = dataUrl;
                a.download = `canvas-page-${activePageIndex + 1}.png`;
                document.body.appendChild(a);
                a.click();
                a.remove();

                stage.scale({ x: oldScale, y: oldScale });
                stage.position(oldPos);
                stage.batchDraw();
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
                salvarNoServidor();
            }
        } else if (e.key === 'ArrowLeft' || e.key === '<') {
            if (activePageIndex > 0) {
                saveCurrentPageShapes();
                loadPage(activePageIndex - 1);
                salvarNoServidor();
            }
        }
    });

    document.getElementById('reset-zoom').addEventListener('click', () => {
        stage.scale({ x: 1, y: 1 });
        stage.position({ x: 0, y: 0 });
        stage.batchDraw();

        if (zoomRange) {
            zoomRange.value = "1.00";
        }
    });

    function salvarNoServidor(isManual = false) {
        saveCurrentPageShapes();

        const titulo = document.getElementById('canvas-title')?.value || 'Projeto salvo';

        const data = {
            pages: pages,
            activePageIndex: activePageIndex
        };

        fetch('/canvas/salvar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                id: window.currentCanvasId || null,
                titulo: titulo,
                data_json: JSON.stringify(data),
                width: pageWidth,
                height: pageHeight
            })
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    window.currentCanvasId = response.id;
                    if (isManual) {
                        alert('Canvas salvo com sucesso!');
                    }
                } else {
                    if (isManual) {
                        alert('Erro ao salvar canvas.');
                    }
                }
            })
            .catch(() => {
                if (isManual) alert('Erro de conexão ao salvar canvas.');
            });
    }

    function carregarCanvasSalvo() {
        if (!window.currentCanvasId) {
            criarCanvasInicial();
            return;
        }

        fetch('/canvas/carregar?id=' + window.currentCanvasId)
            .then(res => res.json())
            .then(data => {
                if (data && data.data) {
                    const d = data.data;
                    pages = d.pages || [{
                        id: 1,
                        width: data.width || 1000,
                        height: data.height || 600,
                        shapes: []
                    }];
                    activePageIndex = d.activePageIndex || 0;
                    pageWidth = data.width || 1000;
                    pageHeight = data.height || 600;
                    window.currentCanvasId = data.id || null;

                    document.getElementById('page-width').value = pageWidth;
                    document.getElementById('page-height').value = pageHeight;

                    if (document.getElementById('canvas-title')) {
                        document.getElementById('canvas-title').value = data.titulo || '';
                    }

                    loadPage(activePageIndex);
                    renderPageThumbnails();
                } else {
                    criarCanvasInicial();
                }
            })
            .catch(() => {
                alert('Erro ao carregar o canvas salvo do servidor.');
                criarCanvasInicial();
            });
    }

    function getShapesJsonWithImages() {
        return layer.getChildren(shape => shape !== paperRect && !(shape instanceof Konva.Transformer)).map(shape => {
            const json = JSON.parse(shape.toJSON());
            if (shape.className === 'Image' && shape.image()) {
                json.attrs.imageBase64 = shape.image().src;
            }
            return json;
        });
    }

    function criarCanvasInicial() {
        pages = [{
            id: 1,
            width: pageWidth,
            height: pageHeight,
            shapes: [],
        }];
        activePageIndex = 0;
        loadPage(activePageIndex);
        renderPageThumbnails();
    }

    carregarCanvasSalvo();
});
