const path = require('path')
var webpack = require('webpack');

// 常用設定
var config = {
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
};

// 前台設定
var receptionConfig = Object.assign({}, config, {
    name: "reception",
    entry: [
        './src/js/reception/index',
        './src/css/alert.css',
        './src/css/tailwind.generated.css',
        './src/css/reception.css',
        'bootstrap-icons/font/bootstrap-icons.css',
    ],
    output: {
        path: path.join(__dirname, './src/dist/reception'),
        filename: 'bundle.js'
    },
});

// 後台設定
var manageConfig = Object.assign({}, config,{
    name: "manage",
    entry: [
        './src/js/manage/index',
        './src/css/alert.css',
        './src/css/tailwind.generated.css',
        './src/css/manage.css',
        './src/css/table.css',
        'datatables.net-dt/css/jquery.dataTables.css',
        'bootstrap-icons/font/bootstrap-icons.css',
    ],
    output: {
        path: path.join(__dirname, './src/dist/manage'),
        filename: 'bundle.js'
    },
});

// 總配置
module.exports = [
    receptionConfig, manageConfig,       
];
