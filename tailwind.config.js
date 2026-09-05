import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fdf8f4',
                    100: '#f9eee4',
                    200: '#f2dac9',
                    300: '#e7bfa4',
                    400: '#d79b76',
                    500: '#c2774d',
                    600: '#a65b38',
                    700: '#87462d',
                    800: '#6f3928',
                    900: '#5b3024',
                    950: '#341810',
                },
                accent: {
                    50: '#fbf8ee',
                    100: '#f5edd4',
                    200: '#ebd7a8',
                    300: '#dfbc74',
                    400: '#d4a148',
                    500: '#c48731',
                    600: '#a96a26',
                    700: '#874e21',
                    800: '#703e20',
                    900: '#5d341d',
                },
            },
        },
    },

    plugins: [forms, typography],
};
