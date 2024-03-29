const path = require('path');

module.exports = {
    output : {chunkFilename: 'js/[name].js?id=[chunkhash]'},
    resolve: {
        alias: {
            '@' : path.resolve('vendor/seatplus/web/resources/js'),
            ziggy: path.resolve('vendor/tightenco/ziggy/dist')
        }
    }
}