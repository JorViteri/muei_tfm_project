// vue.config.js
module.exports = {
  publicPath:
    //process.env.NODE_ENV === 'production' ? '/muei_tfm_project/' : '/',
    '/',

  configureWebpack: {
    devtool: 'source-map'
    //devtool: false
  },

  devServer: {
    proxy: {
      '^/api/': {
        target: 'http://localhost:8000/',
        changeOrigin: true
      }
    }
  },

  transpileDependencies: ['vuetify']
};
