import './bootstrap';
import Alpine from 'alpinejs';
import './appointments.js';

window.Alpine = Alpine;
Alpine.start();

// Lógica del Modo Oscuro
document.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            if (window.lucide) window.lucide.createIcons();
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) window.lucide.createIcons();
});

// ==========================================
// APAGAR EL LOADER
// ==========================================
document.addEventListener('DOMContentLoaded', function () {
    const loader = document.getElementById('global-loader');

    // Si el loader no está oculto (es decir, es la primera vez que entra)
    if (loader && loader.style.display !== 'none') {
        // Guardamos que ya se mostró
        sessionStorage.setItem('loader_mostrado', 'true');

        // Lo ocultamos después de 1.2 segundos
        window.addEventListener('load', function () {
            setTimeout(() => {
                loader.classList.add('opacity-0');
                setTimeout(() => { loader.style.display = 'none'; }, 700);
            }, 1200);
        });
    }
});