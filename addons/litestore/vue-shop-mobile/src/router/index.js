import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

export default new Router({
	routes: [
		{
			path: '/',
			name: 'Index',
			component: resolve => require(['@/components/Index'], resolve),
			children:[
				{
				  path: 'goods',
				  component: resolve => require(['@/components/Goods'], resolve),
				},
				{
					path: 'news',
					component: resolve => require(['@/components/News'], resolve),
				},

			]
		},
		{
			path: '/goods',
			name: 'Goods',
			component: resolve => require(['@/components/Goods'], resolve),
		},
		{
			path: '/news',
			name: 'News',
			component: resolve => require(['@/components/News'], resolve),
		},
		{
			path: '/pageList',
			name: 'PageList',
			component: resolve => require(['@/components/PageList'], resolve),
			children:[
				{
				  path: 'relist',
				  component: resolve => require(['@/components/ReList'], resolve),
				  children:[
				  	{
				  	  path: 'goods',
				  	  component: resolve => require(['@/components/Goods'], resolve),
				  	}
				  ]
				},
			]
		},
		{
			path: '/SetToken',
			name: 'SetToken',
			component: resolve => require(['@/components/SetToken'], resolve),
		},
		{
			path: '/my',
			name: 'My',
			component: resolve => require(['@/components/My'], resolve),
			children:[
						{
							path: 'myorder',
							//name: 'MyOrder',
							component: resolve => require(['@/components/MyOrder'], resolve),
							children:[
							  	{
							  	  path: 'detail',
							  	  component: resolve => require(['@/components/OrderDetail'], resolve),
							  	}
							  ]
						},
			]
		},
		{
			path: '/cartindex',
			name: 'CartIndex',
			component: resolve => require(['@/components/CartIndex'], resolve),
			children:[
					  	{
					  	  path: 'goods',
					  	  component: resolve => require(['@/components/Goods'], resolve),
					  	},
					  	{
					  	  path: 'cartcheck',
					  	  component: resolve => require(['@/components/CartCheck'], resolve),
					  	  children:[
							{
							  path: 'adresslist',
							  component: resolve => require(['@/components/AdressList'], resolve),
							},
							{
							  path: 'adressedit',
							  component: resolve => require(['@/components/AdressEdit'], resolve),
							},
						  ]
					  	}
					]
		},
		{
			path: '/adresslist',
			name: 'AdressList',
			component: resolve => require(['@/components/AdressList'], resolve),
		},
		{
			path: '/adressedit',
			name: 'AdressEdit',
			component: resolve => require(['@/components/AdressEdit'], resolve),
		},
		
	]
})
