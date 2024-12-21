/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.twig",
    ],
    theme: {
        extend: {
            animation: {
                'toast-slide-in': 'toast-slide-in 0.5s',
                'toast-slide-out': 'toast-slide-out 0.5s'
            },
            keyframes: {
                'toast-slide-in': {
                    '0%': {transform: 'translateX(100%)'},
                    '100%': {transform: 'translateX(0)'},
                },
                'toast-slide-out': {
                    '0%': {transform: 'translateX(0)'},
                    '100%': {transform: 'translateX(100%)'},
                },
            }
        },
    },
    plugins: [],
}

