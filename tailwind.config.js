const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './resources/views/**/*.blade.php'
    ],

    safelist: [
        {
            pattern: /bg-(red|green|blue)-(500|700)/,
            variants: ['hover', 'focus'],
        },
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
