import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

import '../css/app.css';

import Konva from 'konva';

window.Konva = Konva;

// Script para toggle da sidebar
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('toggle-sidebar');
    const sidebar = document.getElementById('sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            const expanded = !sidebar.classList.contains('collapsed');
            toggle.setAttribute('aria-expanded', expanded);
        });
    }
});

// Botão para criar novo canvas com prompt de tamanho
document.addEventListener('DOMContentLoaded', () => {
    const btnNewCanvas = document.getElementById('btn-new-canvas');

    btnNewCanvas.addEventListener('click', () => {
        let width = prompt('Digite a largura do canvas (mínimo 100):', '1000');
        let height = prompt('Digite a altura do canvas (mínimo 100):', '600');

        width = parseInt(width);
        height = parseInt(height);

        if (!width || width < 100) width = 1000;
        if (!height || height < 100) height = 600;

        window.location.href = `/editor?new=1&width=${width}&height=${height}`;
    });
});

// Ajusta o tamanho do canvas no editor conforme container
document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('canvas');
    const container = document.getElementById('canvas-container');

    function ajustarTamanhoCanvas() {
        if (!canvas || !container) return;

        const rect = container.getBoundingClientRect();

        canvas.width = rect.width;
        canvas.height = rect.height;
    }

    ajustarTamanhoCanvas();

    window.addEventListener('resize', ajustarTamanhoCanvas);
});

document.addEventListener('DOMContentLoaded', async () => {
    const previews = document.querySelectorAll('.mini-preview');

    previews.forEach(async (div) => {
        const canvasId = div.dataset.canvasId;
        try {
            const response = await fetch(`/canvas/carregar?id=${canvasId}`);
            const result = await response.json();

            if (!result.data) return;

            const previewMaxWidth = 150;
            const page = result.data.pages[0];
            const canvasW = page.width;
            const canvasH = page.height;

            const aspectRatio = canvasW / canvasH;
            const stageWidth = previewMaxWidth;
            const stageHeight = previewMaxWidth / aspectRatio;

            // Ajusta o tamanho do container da miniatura
            div.style.width = `${stageWidth}px`;
            div.style.height = `${stageHeight}px`;

            const scaleX = stageWidth / canvasW;
            const scaleY = stageHeight / canvasH;
            const scale = Math.min(scaleX, scaleY);

            const offsetX = (stageWidth - canvasW * scale) / 2;
            const offsetY = (stageHeight - canvasH * scale) / 2;

            const stage = new Konva.Stage({
                container: div.id,
                width: stageWidth,
                height: stageHeight,
            });

            const layer = new Konva.Layer();
            stage.add(layer);

            page.shapes.forEach(shape => {
                let konvaShape = null;

                const attrs = shape.attrs || {};
                const className = shape.className;

                switch (className) {
                    case 'Rect':
                        konvaShape = new Konva.Rect({
                            x: attrs.x * scale + offsetX,
                            y: attrs.y * scale + offsetY,
                            width: attrs.width * scale,
                            height: attrs.height * scale,
                            fill: attrs.fill || 'gray',
                            stroke: attrs.stroke || 'black',
                            strokeWidth: (attrs.strokeWidth || 1) * scale,
                        });
                        break;

                    case 'Circle':
                        konvaShape = new Konva.Circle({
                            x: attrs.x * scale + offsetX,
                            y: attrs.y * scale + offsetY,
                            radius: attrs.radius * scale,
                            fill: attrs.fill || 'gray',
                            stroke: attrs.stroke || 'black',
                            strokeWidth: (attrs.strokeWidth || 1) * scale,
                        });
                        break;

                    case 'Text':
                        konvaShape = new Konva.Text({
                            x: attrs.x * scale + offsetX,
                            y: attrs.y * scale + offsetY,
                            text: attrs.text || '',
                            fontSize: (attrs.fontSize || 18) * scale,
                            fill: attrs.fill || 'black',
                        });
                        break;

                    case 'Image':
                        if (attrs.imageBase64) {
                            const imageObj = new Image();
                            imageObj.onload = () => {
                                konvaShape = new Konva.Image({
                                    ...attrs,
                                    x: attrs.x * scale + offsetX,
                                    y: attrs.y * scale + offsetY,
                                    width: attrs.width * scale,
                                    height: attrs.height * scale,
                                    image: imageObj,
                                });
                                layer.add(konvaShape);
                                layer.draw();
                            };
                            imageObj.src = attrs.imageBase64;
                            return;
                        }
                        break;
                }

                if (konvaShape) {
                    layer.add(konvaShape);
                }
            });

            layer.draw();
        } catch (err) {
            console.error('Erro ao carregar miniatura:', err);
        }
    });
});
