const path = require('path')
var webpack = require('webpack');

module.exports = {
    // 輸入:要加進webpack的檔案
    entry: [
        './src/js/index',
        './src/css/alert.css',
        './src/css/tailwind.generated.css',
        './src/css/manage.css',
        './src/css/reception.css',
        './src/css/table.css',
        'datatables.net-dt/css/jquery.dataTables.css',
        'bootstrap-icons/font/bootstrap-icons.css',
    ],
    // 輸出:路徑及檔名
    output: {
        path: path.join(__dirname, './src/dist'),
        filename: 'bundle.js'
    },
    module: {
        // loader，除了js以外都要設定相關的loader
        rules: [
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader'],
            },
            {
                test: /\.(woff(2)?|png|jpe?g|gif)$/i,
                use: [{
                    loader: 'file-loader',
                },],
            }
        ]
    },
    plugins:[
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery',
            'window.jQuery': 'jquery',
        }),
    ]
}