import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Gradients and backgrounds used dynamically in Blade
        'from-sky-400', 'to-sky-600', 'from-cyan-400', 'to-cyan-600', 'from-blue-400', 'to-blue-600',
        'bg-sky-200', 'bg-cyan-200', 'bg-blue-200', 'bg-sky-50', 'bg-sky-500',
        // Borders/rings/colors for selected states
        'border-sky-500', 'ring-sky-400', 'ring-sky-500', 'text-sky-600', 'hover:border-sky-300',
        // Peer-checked variants used for selection highlights
        'peer-checked:border-sky-500', 'peer-checked:bg-sky-50', 'peer-checked:ring-2', 'peer-checked:ring-sky-400', 'peer-checked:ring-sky-500', 'peer-checked:shadow-md', 'peer-checked:flex',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
