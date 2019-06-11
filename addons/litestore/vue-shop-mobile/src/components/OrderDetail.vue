<template>
	<div class="orderdetail">
		<cube-page type="orderdetail-view" title="订单详情">
			<div slot="content">
				<van-steps :active="active" :active-color="gcfg.BackgroundColor" class="vsp-c">
					<van-step>付款中<br>请及时支付</van-step>
					<van-step>待发货<br>后台配货中</van-step>
					<van-step>已发货<br>快递狂奔中</van-step>
					<van-step>已完成<br>享受宝物中</van-step>
				</van-steps>

				<div class="card-root-c" v-for="(item, idx) in detail.goods" :key="idx">
			      <div v-if="item.show_error != 0" class="goods_err_div">{{item.show_error_text}}</div>
			      <van-card :price="item.goods_price"
			      :title="item.goods_name" :num="item.total_num"
			      :desc="item.goods_attr ? item.goods_attr :'默认规格'"
			      :centered="true" :lazy-load="true" :thumb="item.spec.spec_image==''? item.image:item.sku_image">
			      </van-card>
			  	 </div>

			  	  <!-- 这里是总计 和 邮费 -->
				  <van-cell-group class="vcg-c">
				  	<van-cell title="订单号：" :value="detail.order_no" />
				  	<van-cell title="订单提交时间：" :value="detail.creattime_text" />
				  	<van-cell v-if="detail.pay_status==20" title="订单支付时间：" :value="detail.pay_time_text" />
				    <van-cell title="商品价格总计：" class="pric-cell" :value="'￥'+detail.total_price" />
				    <van-cell title="快递费用：" class="pric-cell" :value="detail.express_price==0 ? '免邮费或未查询到运费' : '＋ ￥' + detail.express_price" />

				    <!-- 在已发货后 可以看到 发货时间，发货单号 运送公司 -->
				    <div v-if="detail.freight_time!=0">
				      <van-cell title="发货时间：" :value="detail.freight_time_text" />
				      <van-cell title="快递公司：" :value="detail.express_company" />
				      <van-cell title="快递单号：" :value="detail.express_no" />
				    </div>
				  </van-cell-group>

				  <van-submit-bar
					  :price="detail.pay_price*100"
					  :button-text="detail.BTText" :button-type="detail.BTtype"
					  @submit="onSubmit" :loading="disabled" :disabled="active==1 || active==3" >
					  <van-tag size="large" v-if="active==0" class="tagadress" :color="gcfg.BackgroundColor" @click="TapCancel">取消订单</van-tag>
					  <span slot="tip">
					    <van-icon class='ico-ad' name="logistics"/><span class='ab_mt'>{{detail.address.Area.province}}{{detail.address.Area.city}}{{detail.address.Area.region}} - {{detail.address.detail}}</span><br>
	   					<van-icon class='lianxirentip ico-ad' name="phone"/><span class='lianxirentip ab_mt'>{{detail.address.name}} : {{detail.address.phone}}</span>
					  </span>
				  </van-submit-bar>
			</div>
		</cube-page>
	</div>
</template>

<script>
	import CubePage from './TP/cube-page.vue'
	import * as util from '@/utils/network'
	
	export default {
		components: {
			CubePage,
		},
		data() {
			return {
			    active:0,
			    id:this.$route.query.id,
			    detail: {address:{Area:{}},},
			    disabled: false,
			    gcfg:[],
			};
		},
		created: function () {
			var url = "/addons/litestore/api.order/detail"
			let that = this
			
			//这里直接调用网络接口
			util.get(url,{ id: that.id},
				function (result) {
					console.log(result.data)
					 let active = 0;
				      //格式化商品状态
				      var item = result.data.order;
				      if (item.pay_status == "20" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10") {
				        item.showText = "待发货";
				        item.BTText = "已付款";
				        item.BTtype = 'primary';
				        active = 1;
				      }
				      if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "10" && item.receipt_status == "10") {
				        item.showText = "待收货";
				        item.BTText = "确认收货";
				        item.BTtype = 'warning';
				        active = 2;
				      }
				      if (item.pay_status == "10" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10") {
				        item.showText = "待付款";
				        item.BTText = "提交订单";
				        item.BTtype = 'danger';
				        active = 0;
				      }
				      if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "30" && item.receipt_status == "20") {
				        item.showText = "已完成";
				        item.BTText = "订单已完成";
				        item.BTtype = 'default';
				        active = 3;
				      }
				      that.detail=item
				      that.active=active
				},
			);

			this.gcfg = this.$store.getters.getGcfg
		},
		methods: {
			onSubmit(){
				//提交订单
				let that = this
				if (that.disabled) {
				     return false;
				}
				that.disabled = true;

				//如果是确认收货
			    if (that.active == 2 ){
			      var url = "/addons/litestore/api.order/finish"
			      util.post(url,{'id': that.id}, 
					function (result) {
				    	that.disabled = false;
				    	that.$router.go(0);
					},
					function (result) {
						that.disabled = false;
					    console.log(result);
					},
				  );
			      
			      return;
			    }

				//提交订单
				var url = "/addons/litestore/api.order/order_pay"
				util.post(url,{type:'gzh','id': that.id}, 
					function (result) {
				    	console.log(result);
						that.disabled = false;
						that.wx_pay_fun(result.data);
					},
					function (result) {
						that.disabled = false;
					    console.log(result);
					    },
				);

			},
			TapCancel(){
				var url = "/addons/litestore/api.order/cancel"
				let that = this

				that.$dialog.confirm({
					  title: '提示',
					  message: '确认取消订单？'
					}).then(() => {
						//这里直接调用网络接口
						util.post(url,{ id: that.id},
							function (result) {
								console.log(result.data)
								that.$router.go(-1);
							},
						);
					}).catch(() => {

					});
			},
			wx_pay_fun(re){
				var vm = this;
				if (typeof WeixinJSBridge == "undefined"){//微信浏览器内置对象。参考微信官方文档
				  vm.$toast('请使用微信打开');
				  if( document.addEventListener ){
					document.addEventListener('WeixinJSBridgeReady', vm.onBridgeReady(re), false);
				  }else if (document.attachEvent){
					document.attachEvent('WeixinJSBridgeReady', vm.onBridgeReady(re));
					document.attachEvent('onWeixinJSBridgeReady',vm.onBridgeReady(re));
				  }
				}else{
				  vm.onBridgeReady(re);
				}
			},
			onBridgeReady(data){
				var vm = this;
				//alert(JSON.stringify(data))
				WeixinJSBridge.invoke(
				  'getBrandWCPayRequest',{
					  	"appId": data.appId,
						"nonceStr": data.nonceStr,
						"package": data.package,
						"signType": data.signType,
						"paySign": data.paySign,
						"timeStamp": data.timestamp
				  },
				  function(res){
					console.log(res.err_msg)
					if(res.err_msg == "get_brand_wcpay_request:ok" ){
						vm.$toast('恭喜，支付成功');

					}else{
						vm.$toast('未支付或支付失败');
					}
					vm.$router.go(0)
				  }
				);
			}
		},
		watch:{
		  "$store.state.gcfg": function(){
		  	this.gcfg = this.$store.getters.getGcfg
		  }
		},
	}
</script>

<style>
.orderdetail{
    height: 100%;
    position: absolute;
    width: 100%;
    top: 0;
    z-index: 68;
}
.orderdetail .cube-page >.wrapper .content {
    margin: 0;
}
.orderdetail .van-step--horizontal.van-step--finish .van-step__circle, .van-step--horizontal.van-step--finish .van-step__line {
    background-color: rgb(109, 24, 158)
}
.orderdetail .van-step__circle-container{
    top: 48px;
}
.orderdetail .van-step--horizontal .van-step__line {
	top: 50px;
}
.orderdetail .van-step--horizontal.van-step--process .van-step__circle-container {
    top: 44px;
}
.orderdetail .pric-cell .van-cell__value span{
  color:#f44;
}
.orderdetail .tagadress{
	margin-left: 1em;
}
.orderdetail .vcg-c.van-cell-group.van-hairline--top-bottom {
    margin-bottom: 7em;
}
</style>
