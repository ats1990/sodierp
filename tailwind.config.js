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
                // Paleta Personalizada
                'cinza-bacalhau': '#060606', // O seu preto/texto escuro
                'flamingo': '#f47034',      // A sua cor de destaque (Primary)
                'pampas': '#f3f1ed',        // O seu fundo claro/branco sujo
                'pedregulho': '#c3c3c3',    // Exemplo de cinza médio/neutro (Ajuste se souber o código)
                
                // Opcional: Manter o nome 'primary' para facilidade
                'primary': '#f47034',
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
