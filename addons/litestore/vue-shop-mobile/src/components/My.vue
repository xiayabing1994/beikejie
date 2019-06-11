<template>
	<div>
		<div class="header">
		    <div style="text-align: center;font-size: 0.8em; color: white; padding-top: 1em; margin-bottom: 1.8em;">
		       个人中心
		    </div>
		    <div class="avatar">
		      <img style="width: 100%;" :src='user.avatar'></img>
		    </div>
		    <div class="account">
		      <van-button style="margin-bottom: 1em;" plain type="primary" size="small" v-if="third==null" @click="binding_wx">绑定微信</van-button>
		      <div v-else class="showname">
		          {{ user.nickname }}
		      </div>
		    </div>
		</div>

		<van-cell-group class="buttons_froup">
		  <van-cell title="收货地址管理" icon="location" to="AdressList" is-link />
		  <van-cell title="全部订单" icon="records" to="./my/myorder?showType=0" is-link />
		  <van-cell icon="after-sale" to="./my/myorder?showType=1" is-link>
		    <div slot="title">
		      <span>待付款订单</span>
		      <van-tag class="tag-c-c" v-if="datanum.NoPayNum!=0" color="#6d189e">{{datanum.NoPayNum}}</van-tag>
		    </div>
		  </van-cell>
		  <van-cell icon="free-postage" to="./my/myorder?showType=2" is-link>
		    <div slot="title">
		      <span>待发货订单</span>
		      <van-tag class="tag-c-c" v-if="datanum.NoFreightNum!=0" color="#6d189e">{{datanum.NoFreightNum}}</van-tag>
		    </div>
		  </van-cell>
		  <van-cell icon="completed" to="./my/myorder?showType=3" is-link>
		    <div slot="title">
		      <span>待收货订单</span>
		      <van-tag class="tag-c-c" v-if="datanum.NoReceiptNum!=0" color="#6d189e">{{datanum.NoReceiptNum}}</van-tag>
		    </div>
		  </van-cell>
		</van-cell-group>

		<Copyright style="margin-top:4em"></Copyright>
				
		<BottomTabbar :curactive="3"></BottomTabbar>
		<cube-view></cube-view>
	</div>
</template>

<script>
	import CubeView from '@/components/TP/cube-view'
	import * as util from '@/utils/network'
	import BottomTabbar from '@/components/TP/BottomTabbar'
	import Copyright from '@/components/TP/Copyright'
	
	export default {
		data() {
			return {
				 id:this.$route.query.id,
				 NewsData:[],
				 user:[],
				 third:null,
				 datanum:[],
			}
		},
		components: {
			CubeView,
			BottomTabbar,
			Copyright,
		},
		methods:{
			axios_Request: function() {
				var url = "/addons/litestore/api.uservue/index"
				let that = this;
				
				//这里直接调用网络接口
				util.get(url,{},
					function (result) {
						console.log(result);
						that.user = result['data']['user']
						that.third = result['data']['third']
					},
				);
			},
			binding_wx:function(){
				var urltmp = 'http://'+window.location.host+'/addons/litestore/api.uservue/connect?platform=wechat&url='+
									 'http://'+window.location.host+'/addons/litestore/octothorpe/My'; 
				window.location.href = urltmp
			}
		},
		created: function () {
			this.axios_Request();
		},
		mounted: function () {
			var url = "/addons/litestore/api.order/Get_order_num"
			let that = this;
			
			//这里直接调用网络接口
			util.get(url,{},
				function (result) {
					console.log(result);
					that.datanum = result['data']
				},
			);
		},
	}
</script>

<style>
.header {
  width:100%;
  background: #6d189e;
  top:0;
}

.header .avatar {
  border-radius: 50%;
  overflow: hidden;
  width: 80px;
  height: 80px;
  text-align: center;
  background: #fff;
  margin: 15px auto;
  border: 2px solid #fff;
  box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
}
.header .avatar image {
  width: 100%;
  height: 100%;
}
.header .account {
  color: #fff;
  text-align: center;
}
.showname{
  font-size: 1.2em;
  padding-bottom: 0.8em;
}
.title-footer .cont {
  background-color:#f8f8f8;
}
.buttons_froup{
  margin-top: 1em;
}

.title-footer{
  margin-top:1em;
}
.van-cell__left-icon, .van-cell__right-icon {
   font-size: 1.5em;
   color:#6d189e;
}
.tag-c-c{
	margin-left: 1em;
}
</style>