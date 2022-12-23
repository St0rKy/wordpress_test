const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts')
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const { VueLoaderPlugin } = require('vue-loader');
var path = require('path');

const jsPath = './js';
const sassPath = './sass';
const cssPath = './css';
const outputPath = 'js/dist';
const localDomain = 'http://localhost';
const entryPoints = {
    'index': jsPath + '/index.js',
    'style': sassPath + '/style.scss',
};

module.exports = {
    entry: entryPoints,
    output: {
        path: path.resolve(__dirname, outputPath),
        filename: '[name].min.js',
    },
    plugins: [
        new VueLoaderPlugin(),
        new RemoveEmptyScriptsPlugin(),
        new MiniCssExtractPlugin({
            filename: '../../css/[name].min.css',
        }),

        new BrowserSyncPlugin({
            proxy: localDomain,
            files: [ cssPath + '/**/*.css', outputPath + '/**/*.js', './**/*.php' ],
            injectCss: true,
            notify: false
        }, { reload: true, }),
    ],
    resolve: {
        extensions: [ '.tsx', '.ts', '.js', '.vue' ],
        alias: {
            'vue': "vue/dist/vue.esm-bundler.js"
        }
    },
    watchOptions: {
        poll: 1000, // Check for changes every second
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                use: 'vue-loader',
            },
            {
                test: /\.s?[c]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader',
                ],
            },
            {
                test: /\.sass$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                            sassOptions: {indentedSyntax: true},
                        },
                    },
                ],
            },
            {
                test: /\.(svg)(\?v=\d+\.\d+\.\d+)?$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]',
                        outputPath: './images'
                    }
                }]
            },
            {
                test: /\.(jpg|jpeg|png|gif)$/i,
                use: [
                    {
                        loader: 'url-loader',
                        options: {
                            encoding: true,
                        },
                    },
                ],

            },
        ]
    },
};