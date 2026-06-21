// Fix webpack publicPath auto-detect fail on defer scripts
// Use '/' because chunkFilename already has 'js/' prefix → '/js/140.js' correct
__webpack_public_path__ = '/';
