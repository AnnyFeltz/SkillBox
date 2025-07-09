import Konva from 'konva';

console.log('Konva importado:', Konva);
console.log('Elementos no DOM:', document.getElementById('canvas-container'));
console.log('Dimensões iniciais:', window.initialCanvasWidth, window.initialCanvasHeight);

document.addEventListener('DOMContentLoaded', () => {
    let pages = [];
    let activePageIndex = 0;
    let pageWidth = window.initialCanvasWidth || 1000;
    let pageHeight = window.initialCanvasHeight || 600;

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
            if (shapeJSON.className === 'Image' && shapeJSON.attrs.imageUrl) {
                imagesToLoad++;
                const imageObj = new Image();
                imageObj.crossOrigin = "Anonymous"; // <---- ESSA LINHA AQUI
                imageObj.onload = () => {
                    const konvaImage = new Konva.Image({
                        ...shapeJSON.attrs,
                        image: imageObj,
                    });
                    layer.add(konvaImage);

                    konvaImage.on('click', () => {
                        selectedShape = konvaImage;
                        tr.nodes([selectedShape]);
                        updatePropertiesPanel(selectedShape);
                        document.getElementById('delete-selected').disabled = false;
                        layer.draw();
                    });

                    imagesLoaded++;
                    checkAllImagesLoaded();
                };
                imageObj.onerror = () => {
                    console.warn('Erro ao carregar imagem com crossOrigin:', shapeJSON.attrs.imageUrl);
                    imagesLoaded++;
                    checkAllImagesLoaded();
                }
                imageObj.src = shapeJSON.attrs.imageUrl;
            } else {
                const shape = Konva.Node.create(shapeJSON);
                layer.add(shape);

                shape.on('click', () => {
                    selectedShape = shape;
                    tr.nodes([selectedShape]);
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

    const IMGBB_API_KEY = window.IMGBB_API_KEY;

    async function uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch(`https://api.imgbb.com/1/upload?key=${IMGBB_API_KEY}`, {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                const imageUrl = data.data.url;

                const imageObj = new Image();
                imageObj.crossOrigin = "Anonymous"; // ESSENCIAL para evitar tainted canvas
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

                    pages[activePageIndex].shapes.push({
                        className: 'Image',
                        attrs: {
                            imageUrl: imageUrl,
                            x: 50,
                            y: 50,
                            width: imageObj.width / 2,
                            height: imageObj.height / 2,
                            draggable: true
                        }
                    });

                    saveCurrentPageShapes();
                    salvarNoServidor();
                };
                imageObj.onerror = () => {
                    alert('Erro ao carregar a imagem após upload.');
                }
                imageObj.src = imageUrl;
            } else {
                alert('Erro ao enviar imagem para ImgBB: ' + (data.error.message || 'Desconhecido'));
            }
        } catch (error) {
            console.error('Erro no upload da imagem:', error);
            alert('Erro ao enviar imagem para ImgBB.');
        }
    }

    document.getElementById('publish').addEventListener('click', publishProject);


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
            width: pageWidth,
            height: pageHeight,
            x: 0,
            y: 0,
            pixelRatio: 2,
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

    async function salvarNoServidor(isManual = false) {
        saveCurrentPageShapes();

        const titulo = document.getElementById('canvas-title')?.value || 'Projeto salvo';

        try {
            // Ajusta escala e posição para gerar o PNG limpo da página ativa
            const oldScale = stage.scaleX();
            const oldPos = stage.position();

            stage.scale({ x: 1, y: 1 });
            stage.position({ x: 0, y: 0 });
            stage.batchDraw();

            // Gera o DataURL da imagem PNG do canvas atual
            const dataURL = stage.toDataURL({ pixelRatio: 2 });

            // Remove o prefixo do dataURL para ficar só o base64 puro
            const base64String = dataURL.replace(/^data:image\/(png|jpeg);base64,/, '');

            // Restaura escala e posição antigas
            stage.scale({ x: oldScale, y: oldScale });
            stage.position(oldPos);
            stage.batchDraw();

            // Envia a imagem para o ImgBB via API
            const imgbbResponse = await fetch(`https://api.imgbb.com/1/upload?key=${IMGBB_API_KEY}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    image: base64String
                })
            });

            const imgbbData = await imgbbResponse.json();

            if (!imgbbData.success) {
                if (isManual) alert('Erro ao enviar preview para ImgBB.');
                console.error('Erro ImgBB:', imgbbData);
                return;
            }

            const previewUrl = imgbbData.data.url;

            // Agora salva os dados do canvas no backend, junto com o preview_url
            const saveResponse = await fetch('/canvas/salvar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: window.currentCanvasId || null,
                    titulo: titulo,
                    data_json: JSON.stringify({
                        pages: pages,
                        activePageIndex: activePageIndex
                    }),
                    width: pageWidth,
                    height: pageHeight,
                    preview_url: previewUrl  // envia o link do preview pro backend
                })
            });

            const saveResult = await saveResponse.json();

            if (saveResult.success) {
                window.currentCanvasId = saveResult.id;
                if (isManual) alert('Canvas salvo com sucesso!');
            } else {
                if (isManual) alert('Erro ao salvar canvas.');
            }
        } catch (err) {
            console.error('Erro ao salvar canvas:', err);
            if (isManual) alert('Erro ao salvar canvas no servidor.');
        }
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
                json.attrs.imageUrl = shape.image().src;
                delete json.attrs.imageBase64;
            }
            return json;
        });
    }

    async function publishProject() {
        saveCurrentPageShapes();

        const images = [];

        for (let i = 0; i < pages.length; i++) {
            loadPage(i); // Carrega a página
            await new Promise(resolve => setTimeout(resolve, 500)); // Espera 500ms para renderizar

            try {
                // Garante escala e posição para exportar limpo
                const oldScale = stage.scaleX();
                const oldPos = stage.position();

                stage.scale({ x: 1, y: 1 });
                stage.position({ x: 0, y: 0 });
                stage.batchDraw();

                const dataURL = stage.toDataURL({ pixelRatio: 2 });
                const imageData = dataURL.split(',')[1]; // só o base64, sem o prefixo

                stage.scale({ x: oldScale, y: oldScale });
                stage.position(oldPos);
                stage.batchDraw();

                const res = await fetch(`https://api.imgbb.com/1/upload?key=${IMGBB_API_KEY}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        image: imageData
                    })
                });

                const data = await res.json();

                if (data.success) {
                    images.push(data.data.url);
                } else {
                    console.error('Erro ao enviar imagem para ImgBB:', data);
                    alert('Erro ao enviar uma das imagens para ImgBB. Veja o console.');
                    return;
                }
            } catch (error) {
                console.error('Erro no upload da imagem:', error);
                alert('Erro ao enviar imagem para ImgBB.');
                return;
            }
        }

        const previewUrl = images[0] || null;

        try {
            const response = await fetch(`/canvas/${window.currentCanvasId}/publicar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    preview_url: previewUrl,
                    imagens: images
                })
            });

            const result = await response.json();

            if (result.success) {
                alert('Publicado com sucesso!');
                location.reload();
            } else {
                alert('Erro ao publicar o projeto.');
                console.error('Erro na resposta do servidor:', result);
            }
        } catch (err) {
            console.error('Erro ao enviar dados para publicar:', err);
            alert('Erro ao publicar o projeto.');
        }
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
