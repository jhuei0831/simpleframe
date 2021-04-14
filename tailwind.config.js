module.exports = {
    purge: [],
    darkMode: false, // or 'media' or 'class'
    theme: {
        screens: {
            'xs': '375px',
        },
    },
    variants: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
}
