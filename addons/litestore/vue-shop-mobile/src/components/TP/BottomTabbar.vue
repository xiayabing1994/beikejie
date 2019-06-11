<template>
<div>
	<van-tabbar v-model="active" :style="'background-color:'+gcfg.BackgroundColor">
		<van-tabbar-item icon="shop" to="/">首页</van-tabbar-item>
		<van-tabbar-item icon="bars" to="PageList">搜索</van-tabbar-item>
		<van-tabbar-item icon="cart" to="CartIndex">购物车</van-tabbar-item>
		<van-tabbar-item icon="manager" to="My">我的</van-tabbar-item>
	</van-tabbar>
</div>
</template>

<script>
  import * as util from '@/utils/network'

  export default {
		props: ["curactive"],
		data() {
			return {
				active:this.curactive,
				gcfg:[]
			};
		},
		methods: {
		},
		created: function() {
			this.gcfg = this.$store.getters.getGcfg
		},
		watch:{
		  "$store.state.gcfg": function(){
		  	this.gcfg = this.$store.getters.getGcfg
		  }
		},
  }
  
  
</script>

<style>
.van-tabbar-item--active {
		color: white !important;
		font-weight: bold;
}
.van-tabbar-item{
	  color: #c1c1c1;
}
.van-popup--bottom{
	margin-bottom: 3.9em;
	width: auto;
}

.am-modal-hd {
    padding: 15px 10px 5px 10px;
    font-weight: 500;
}
.am-modal-bd {
    padding: 0 10px 15px;
    text-align: center;
}
.am-share-wechat-qr {
    font-size: 14px;
    color: #777;
}
.am-share-wx-qr img {
    width: 250px!important;
}
.am-share-wechat-tip {
    /* text-align: left; */
    font-size: 12px;
    line-height: 20px;
}
.am-share-wechat-tip em {
    color: #dd514c;
    font-weight: 700;
    font-style: normal;
    margin-left: 3px;
    margin-right: 3px;
}
</style>
