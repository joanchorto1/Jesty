import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './resources/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                neutral: {
                    50: '#f8f7f4',
                    100: '#f0eee9',
                    200: '#e2dfd8',
                    300: '#cbc6bc',
                    400: '#b0aa9e',
                    500: '#918a7d',
                    600: '#736c61',
                    700: '#5a544b',
                    800: '#3e3a34',
                    900: '#27241f',
                },
                accent: {
                    50: '#f3f8ff',
                    100: '#e6f0ff',
                    200: '#cfe0ff',
                    300: '#a6c6ff',
                    400: '#7daaff',
                    500: '#5a8bff',
                    600: '#4a6fe6',
                    700: '#3f5abf',
                },
                surface: 'var(--color-surface)',
                'surface-muted': 'var(--color-surface-muted)',
                text: 'var(--color-text)',
                'text-muted': 'var(--color-text-muted)',
                border: 'var(--color-border)',
            },
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                sm: '0.375rem',
                md: '0.5rem',
                lg: '0.75rem',
                xl: '1rem',
                '2xl': '1.25rem',
                pill: '999px',
            },
            boxShadow: {
                soft: '0 1px 2px 0 rgb(16 24 40 / 0.06), 0 1px 3px 0 rgb(16 24 40 / 0.1)',
                'soft-md':
                    '0 4px 8px -2px rgb(16 24 40 / 0.08), 0 2px 4px -2px rgb(16 24 40 / 0.06)',
                'soft-lg': '0 12px 24px -6px rgb(16 24 40 / 0.12)',
            },
        },
    },

    plugins: [forms, typography],
};
