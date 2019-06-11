const config = require("./WebConfig"); 

module.exports = {
	indexPath: '../../../../../addons/litestore/view/index/vue-mobile.html',
	//baseUrl:'assets/addons/litestore/vue-mobile/',
	publicPath:'assets/addons/litestore/vue-mobile/',
	outputDir: '../../../public/assets/addons/litestore/vue-mobile',
	assetsDir:'',		//bulid发布后，资源的导出目录
	devServer: {
	    open: true,
	    host: '0.0.0.0',
	    port: 8081,
	    https: false,
	    hotOnly: false,
			proxy: {
				[config.ROOT]: {  
					target: config.PROXYROOT,  // 接口域名
					changeOrigin: true, //跨域  
					pathRewrite: {  
						[`^${config.ROOT}`]: ''   //需要rewrite的
						//'^/api': '/' //这里理解成用‘/api’代替target里面的地址，
					},
				}  
			},
	    before: app => {
	    },
		overlay: {//禁用编译检查
				warnings: false,
				errors: false
		},
  	},
	lintOnSave: false//禁用编译检查
}
