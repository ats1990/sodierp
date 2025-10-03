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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Paleta Flamingo Pampas
                brand: '#fb6a28',        // Laranja principal
                'brand-dark': '#140c0b', // Preto/Marrom escuro
                'brand-light': '#ffe5d1', // Tom claro de apoio
                'brand-muted': '#f7c8a5', // Tom médio
                'brand-hover': '#ff8a45', // Hover ou destaque
            },
        },
    },

    corePlugins: {},

    plugins: [
        forms,
        function ({ addUtilities }) {
            addUtilities({
                '[x-cloak]': {
                    display: 'none !important',
                },
            });
        },
    ],
};
