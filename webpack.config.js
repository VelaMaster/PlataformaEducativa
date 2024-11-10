const path = require('path');

module.exports = {
    entry: './calendario.js', // Archivo de entrada  CALENDARIO PROFESOR NO TOCAR
    output: {
        filename: 'bundle.js', // Archivo de salida compilado
        path: path.resolve(__dirname, 'dist') // Directorio de salida
    },
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader'
                }
            }
        ]
    }
};
