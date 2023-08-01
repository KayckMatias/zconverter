const colors = require('tailwindcss/colors')

module.exports = {
    prefix: "tw-",
    darkMode: 'class',
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: colors.blue,
            },
        },
    },
    plugins: [],
};
