const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const webpackPlugins = defaultConfig.plugins
webpackPlugins.push(
  new MiniCssExtractPlugin({
    // Options similar to the same options in webpackOptions.output
    // all options are optional
    filename: '[name].css',
    chunkFilename: '[id].css',
    ignoreOrder: false, // Enable to remove warnings about conflicting order
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
        exclude: /\.module\.s?css$/,
        use: [MiniCssExtractPlugin.loader, "css-loader", "sass-loader"],
      },
      {
        test: /\.(png|jpe?g|gif)$/i,
        loader: "file-loader",
        options: {
          outputPath: "images",
        },
      },
      {
        test: /\.module\.s?css$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: "css-loader",
            options: {
              modules: true,
            },
          },
          "sass-loader",
        ],
      }
    ]
  },
  entry: {
    './assets/backend' : './src/backend/index.js',
    // './assets/frontend' : './src/js/frontend/index.js',
    // './assets/frontend-edit' : './src/js/frontend/edit.js',
  },
  //devtool: 'cheap-eval-source-map',
  externals: {
    jquery: 'jQuery'
  }
};
