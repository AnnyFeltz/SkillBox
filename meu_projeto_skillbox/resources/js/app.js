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


