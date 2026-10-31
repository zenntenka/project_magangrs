import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50:  '#F0FAF9',
                    100: '#D5F2EF',
                    500: '#36B3AB',
                    600: '#2A9A93',
                    700: '#227C77',
                    800: '#2A6F7A',
                    900: '#111827',
                },
                // Palet indigo dipetakan ke teal, sehingga semua class indigo-*
                // di view yang sudah ada otomatis berubah ke warna tema RS.
                indigo: {
                    50:  '#F0FAF9',
                    100: '#D5F2EF',
                    200: '#AAE5DF',
                    300: '#77D3CB',
                    400: '#4FC1B9',
                    500: '#36B3AB',
                    600: '#2A9A93',
                    700: '#227C77',
                    800: '#1E6561',
                    900: '#1A504D',
                },
            },
        },
    },

    plugins: [forms],
};