import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: '#f8f6ff',
                    100: '#f1ecff',
                    200: '#e4daff',
                    300: '#d1bfff',
                    400: '#b898ff',
                    500: '#9b6eff', // 更溫和的紫色
                    600: '#8a57ff',
                    700: '#7c3aed',
                    800: '#6b21a8',
                    900: '#581c87',
                    950: '#3b0764',
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
