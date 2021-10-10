const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const ManifestPlugin = require('webpack-manifest-plugin');
const dotenv = require('dotenv');
dotenv.config({ path: '../../../../.env' });

const GitRevisionPlugin = require('git-revision-webpack-plugin');
const gitRevisionPlugin = new GitRevisionPlugin();

module.exports = {
	mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
	resolve: {
		modules: ['ts', 'node_modules'],
		extensions: ['.ts', '.tsx','.js']
	},
	entry: {
		'app':'./ts/app.tsx',
	},
	devtool: 'source-map',
	devServer: {
		contentBase: './dist'
	},
	watchOptions: {
		aggregateTimeout: 200,
		poll: 1000,
	},
	output: {
		filename: '[name].bundle.js',
		path: path.resolve(__dirname, 'dist')
	},
	module: {
		rules: [
			{
				test: /\.ts(x?)$/,
				exclude: /node_modules/,
				use: [
					{
						loader: 'ts-loader'
					}
				]
			},
			{
				enforce: 'pre',
				test: /\.js$/,
				loader: 'source-map-loader'
			},
			{
				test: /\.s?[ac]ss$/,
				use: [
					MiniCssExtractPlugin.loader,
					{ loader: 'css-loader', options: { url: false, sourceMap: true } },
					{ loader: 'sass-loader', options: { sourceMap: true } }
				],
			}
		]
	},
	externals: {
		'mapbox-gl': 'mapboxgl'
	},
	plugins: [
		new MiniCssExtractPlugin({
			filename: 'style.css'
		}),
	    new webpack.DefinePlugin({
	        MAPBOX_API_ACCESS_TOKEN: `'${process.env.MAPBOX_API_ACCESS_TOKEN}'`,

	        OLO_API_BASE_URL: `'${process.env.OLO_API_BASE_URL}'`,
	        OLO_MAKE_LABEL: `'${process.env.OLO_MAKE_LABEL}'`,
	        OLO_MODEL_LABEL: `'${process.env.OLO_MODEL_LABEL}'`,
	        OLO_COLOR_LABEL: `'${process.env.OLO_COLOR_LABEL}'`,

	        SPREEDLY_ENVIRONMENT_KEY: `'${process.env.SPREEDLY_ENVIRONMENT_KEY}'`,
	        SENTRY_DSN: `'${process.env.SENTRY_DSN}'`,
	        SENTRY_ENVIRONMENT: `'${process.env.SENTRY_ENVIRONMENT}'`,
	        SENTRY_RELEASE: `'${gitRevisionPlugin.commithash()}'`
	    }),
		new ManifestPlugin({
			generate: (seed, files, entrypoints) => files.reduce((manifest, {name, path, chunk}) => ({
				...manifest,
				[name]: { file: path, hash: chunk ? chunk.hash : undefined },
			}), seed),
		}),
		new GitRevisionPlugin()
	]
};
