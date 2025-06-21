import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

import '../css/app.css';


//script to handle sidebar toggle functionality
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

document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('canvas');
  const container = document.getElementById('canvas-container');

  function ajustarTamanhoCanvas() {
    if (!canvas || !container) return;

    const rect = container.getBoundingClientRect();

    // Ajusta a largura e altura do canvas em pixels
    canvas.width = rect.width;
    canvas.height = rect.height;

    // Se tiver contexto e precisar redesenhar, faça aqui:
    // const ctx = canvas.getContext('2d');
    // ctx.clearRect(0, 0, canvas.width, canvas.height);
    // redesenha seus elementos se precisar
  }

  ajustarTamanhoCanvas();

  window.addEventListener('resize', ajustarTamanhoCanvas);
});

