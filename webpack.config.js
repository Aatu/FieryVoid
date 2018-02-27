const path = require('path');

module.exports = {
    mode: "production",
    entry: './source/public/client/UI/reactJs/UI.js',
    output: {
        path: path.resolve(__dirname, 'source/public/client/UI/reactJs/'),
        filename: 'UI.bundle.js'
    }
};