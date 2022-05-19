const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const CopyPlugin = require("copy-webpack-plugin");
const path = require('path');

const mode = process.env.NODE_ENV || 'development';
const prod = mode === 'production';

module.exports = {
	entry: {
		'bundle': ['./src/main.js']
	},
	resolve: {
		alias: {
			svelte: path.dirname(require.resolve('svelte/package.json'))
		},
		extensions: ['.mjs', '.js', '.svelte'],
		mainFields: ['svelte', 'browser', 'module', 'main']
	},
	output: {
		path: path.join(__dirname, '/public'),
		filename: '[name].js',
		chunkFilename: '[name].[id].js',
		clean: true
	},
	module: {
		rules: [
			{
				test: /\.svelte$/,
				use: {
					loader: 'svelte-loader',
					options: {
						compilerOptions: {
							dev: !prod
						},
						emitCss: prod,
						hotReload: !prod
					}
				}
			},
			{
				test: /\.css$/,
				use: [
					{
						loader: MiniCssExtractPlugin.loader,
						options: {
							publicPath: ''
						}
					},
					{ loader: 'css-loader' }
				]
			},
			{
				// required to prevent errors from Svelte on Webpack 5+
				test: /node_modules\/svelte\/.*\.mjs$/,
				resolve: {
					fullySpecified: false
				}
			},
			{
				test: /\.(png|svg|jpg|gif)$/,
				use: {
					loader: 'file-loader',
					options: {
          	outputPath: 'img'
        	}
				}
			}
		]
	},
  optimization: {
		minimize: true,
    minimizer: [
			new TerserPlugin(),
      new CssMinimizerPlugin()
    ]
  },
	mode,
	plugins: [
		new MiniCssExtractPlugin({
			filename: '[name].css'
		}),
		new CopyPlugin({
			patterns: [
				{
          from: "*",
          to: path.join(__dirname, '/public/'),
					context: 'templates/'
        }
			]
		})
	],
	devtool: prod ? false : 'source-map',
	devServer: {
		hot: false,
		static: {
			directory: path.join(__dirname, 'src'),
		},
		port: 8080,
    compress: false
	}
};
