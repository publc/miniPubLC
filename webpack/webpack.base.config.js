const path = require('path');

module.exports = {
  entry: './source/js/app.js',
  output: {
    path: path.resolve(__dirname, '../public'),
    filename: 'js/app.js'
  },
  resolve: {
    extensions: ['.js', '.vue', '.scss']
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
        loader: 'vue-loader',
        options: {
          extractCSS: process.env.NODE_ENV === 'production'
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/,
        options: {
          presets: ['@babel/preset-env']
        }
      },
      {
        test: /\.(png|jpg|svg|gif)$/,
        loader: 'file-loader',
        options: {
          name: '[name].[ext]',
          outputPath: 'img/'
        }
      }
    ]
  }
}