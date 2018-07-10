const path = require('path');

module.exports = {
  entry: './source/js/app.js',
  output: {
    path: path.resolve(__dirname, './public'),
    filename: 'js/app.js'
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          'css-loader',
          'sass-loader'
        ]
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader'
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/,
        options: {
          presets: ['@babel/preset-env']
        }
      }
    ]
  },
  devServer: {
    compress: true,
    overlay: true
  }
}