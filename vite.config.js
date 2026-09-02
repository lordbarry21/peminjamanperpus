import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

// Konfigurasi Vite: Bundler aset frontend super cepat untuk meng-compile CSS & JS
export default defineConfig({
    plugins: [
        laravel({
            // File entry point utama aset frontend yang akan di-bundle
            input: [
                'resources/css/app.css', // File styling utama (Tailwind CSS)
                'resources/js/app.js'    // File skrip JavaScript utama (termasuk Alpine.js)
            ],
            // Mengaktifkan fitur Hot Module Replacement (HMR) & refresh browser otomatis saat ada perubahan file Blade/CSS/JS
            refresh: true,
        }),
    ],
});
