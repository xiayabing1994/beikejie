<template>
	<view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				
			};
		},
		methods:{
			setCookie:function (name,value)
			{
				var Days = 30;
				var exp = new Date();
				exp.setTime(exp.getTime() + Days*24*60*60*1000);
				document.cookie = name + "="+ escape (value) + ";expires=" + exp.toGMTString()+";path=/";
			}
		},
		created: function () {
			//首先获得token数据和url数据，然后存cookie 再跳转
			var $_GET = (function(){
				var url = window.document.location.href.toString();
				var u = url.split("?");
				if(typeof(u[1]) == "string"){
					u = u[1].split("&");
					var get = {};
					for(var i in u){
						var j = u[i].split("=");
						get[j[0]] = j[1];
					}
					return get;
				} else {
					return {};
				}
			})();
			
			this.setCookie('token',$_GET['token']);

			var url = decodeURI($_GET['url'])
			url = unescape(url);
			console.log(url);
			url = url.replace("/octothorpe", '#');
			console.log(url);
			window.location.href = url
		},
	}
</script>

<style>

</style>
