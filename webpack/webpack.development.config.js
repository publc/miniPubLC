const merge = require('webpack-merge');
const base = require('./webpack.base.config');
const config = require('./development.config');
const webpack = require('webpack');

module.exports = merge(base, {
  devServer: {
    compress: true,
    overlay: true
  },
  plugins: [
    new webpack.DefinePlugin({
      'process': {
        env: config
      }
    })
  ]
});