/* 設定有使用到tailwindcss的檔案，production會排除purge以外的檔案 */
/* 要上線一定要使用production，嚴重影響效能                       */
const colors = require('tailwindcss/colors')

module.exports = {
    purge: [
        './**/*.php',
        './src/js/datatable.js',
        './src/js/init-alpine.js',
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
