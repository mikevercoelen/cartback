const path = require('path')
const pkg = require('./package.json')

const ROOT_PATH = path.resolve(__dirname)
const SRC_PATH = path.resolve(ROOT_PATH, 'src')
const OUTPUT_PATH = path.resolve(ROOT_PATH, 'public')

module.exports = {
  entry: path.resolve(SRC_PATH, 'index.js'),
  module: {
    rules: [
      {
        test: /\.(js)$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      }
    ]
  },
  output: {
    path: OUTPUT_PATH,
    filename: `${pkg.name}.min.js`
  }
}
