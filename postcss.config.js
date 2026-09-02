// Konfigurasi PostCSS: Alat pemroses CSS modern untuk mengkompilasi utility Tailwind CSS
export default {
    plugins: {
        // Plugin Tailwind CSS: Memproses class utility (seperti bg-blue-500, p-4, flex) menjadi CSS murni
        tailwindcss: {},

        // Plugin Autoprefixer: Menambahkan prefix vendor (-webkit-, -moz-) otomatis agar CSS kompatibel di semua browser
        autoprefixer: {},
    },
};
