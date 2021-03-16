const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const path = require( 'path' );
const webpackPlugins = defaultConfig.plugins;
webpackPlugins.push(
  new MiniCssExtractPlugin({

    // Options similar to the same options in webpackOptions.output
    // all options are optional
    filename: '[name].css',
    chunkFilename: '[id].css',
    ignoreOrder: false // Enable to remove warnings about conflicting order
  })
);
module.exports = {
  ...defaultConfig,
  plugins: webpackPlugins,
  module: {
    ...defaultConfig.module,
    rules: [
      ...defaultConfig.module.rules,
      {
        test: /\.s?css$/,
        use: [ MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader' ]
      },
      {
        test: /\.(png|jpe?g|gif)$/i,
        loader: 'file-loader',
        options: {
          outputPath: 'images'
        }
      }
    ]
  },
  resolve: {
    alias: {
      scss: path.resolve( __dirname, 'src/scss/' ),
      lib: path.resolve( __dirname, 'src/lib/' )
    }
  },
  entry: {
    './assets/wizard': path.resolve( __dirname, 'components/wizard/wizard.js' ),
    './assets/layout': path.resolve( __dirname, 'components/layout/layout.js' ),
    './assets/styles': path.resolve( __dirname, 'components/styles/styles.js' ),
    './assets/frontend': path.resolve( __dirname, 'src/frontend/index.js' )
  },

  //devtool: 'cheap-eval-source-map',
  externals: {
    jquery: 'jQuery'
  }
};
