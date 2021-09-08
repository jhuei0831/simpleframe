const colors = require('tailwindcss/colors')

module.exports = {
    // 設定有使用到tailwindcss的檔案，production會排除purge以外的檔案
    purge: [
        './**/*.php',
        './src/js/init-alpine.js'
    ],
    darkMode: false,
    theme: {
        extend: {
            colors: {
                cyan: colors.cyan,
            }
        }
    },
    variants: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/aspect-ratio'),
    ],
}
