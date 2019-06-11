<template>
	<div class="catcheck">
		<cube-page type="catcheck-view" title="订单确认">
		<div slot="content">
			 <div class="card-root-c" v-for="(item, idx) in goods_list" :key="idx">
			      <div v-if="item.show_error != 0" class="goods_err_div">{{item.show_error_text}}</div>
			      <van-card :class="item.show_error == 0 ? '' : 'error_goods'" 
			      :price="item.goods_price"
			      :title="item.goods_name" :num="item.total_num"
			      :desc="item.goods_sku.goods_attr ? item.goods_sku.goods_attr :'默认规格'"
			      :centered="true" :lazy-load="true" :thumb="item.goods_sku.spec_image==''? item.image:item.goods_sku.img_show">
			      </van-card>
		  	 </div>

		  	  <!-- 这里是总计 和 邮费 -->
			  <van-cell-group class="vcg-c">
			    <van-cell title="商品数总计：" :value="order_total_num" />
			    <van-cell title="商品价格总计：" class="pric-cell" :value="'￥'+order_total_price" />
			    <van-cell title="快递费用：" class="pric-cell" :value="express_price==0 ? '免邮费或未查询到运费' : '＋ ￥' + express_price" />
			  </van-cell-group>

			  <van-submit-bar
				  :price="order_pay_price*100"
				  button-text="提交订单"
				  @submit="onSubmit" :loading="disabled"
			  >
				  <van-tag size="large" class="tagadress" color="#6d189e" @click="MangerAdress">管理地址</van-tag>
				  <span slot="tip" v-if="address">
				    <van-icon class='ico-ad' name="logistics"/><span class='ab_mt'>{{address.Area.province}}{{address.Area.city}}{{address.Area.region}} - {{address.detail}}</span><br>
   					<van-icon class='lianxirentip ico-ad' name="phone"/><span class='lianxirentip ab_mt'>{{address.name}} : {{address.phone}}</span>
				  </span>
				  <span slot="tip" v-else>
				    <van-icon class='ico-ad' name="logistics"/><span class='ab_mt'>未曾增加收货地址</span><br>
   					<van-icon class='lianxirentip ico-ad' name="phone"/><span class='lianxirentip ab_mt'>请点击管理地址增加</span>
				  </span>
			  </van-submit-bar>

			<cube-view></cube-view>
		</div>
	</cube-page>
	</div>

</template>

<script>
	import CubePage from './TP/cube-page.vue'
	import CubeView from '@/components/TP/cube-view'
	import * as util from '@/utils/network'
	
	export default {
		components: {
			CubePage,
			CubeView,
		},
		data() {
			return {
				goods_list: [], // 商品列表
			    order_total_num: 0,
			    order_total_price: 0,

    			express_price:0,
    			order_pay_price:0,

    			address:null,
    			disabled: false,

    			has_error:false,
    			error_msg:"",

    			forpagedata:[],
			};
		},
		created: function () {
			this.forpagedata.type = this.$route.query.type
			this.forpagedata.goods_id = this.$route.query.goods_id
			this.forpagedata.goods_num = this.$route.query.goods_num
			this.forpagedata.goods_sku_id = this.$route.query.goods_sku_id

			if(this.$route.query.type=='cart'){
				this.getCartList();
			}
			//事件触发
			this.$root.eventHub.$on('changeadress', (data)=>{
			    if(this.forpagedata.type=='cart'){
			    	this.getCartList();
			    }else{
			    	this.getBuyNowdata();
			    }
			})
			if(this.$route.query.type=='buyNow'){
				this.getBuyNowdata();
			}
		},
		beforeDestroyed() { 
			this.$root.eventHub.$off('changeadress')
		},
		methods: {
				/** 购物车列表 */
				getCartList: function () {
				  	let that = this;
					var url = "/addons/litestore/api.cart/getlists"
					util.get(url,{}, 
						function (result) {
							that.goods_list = result.data.goods_list
							that.order_total_num = result.data.order_total_num
							that.order_total_price = result.data.order_total_price
							that.address = result.data.address
							that.express_price = result.data.express_price
							that.order_pay_price = result.data.order_pay_price

							that.has_error = result.data.has_error
							that.error_msg = result.data.error_msg
							//提示下架的商品
					        if (result.data.error_msg) {
					        	that.$toast.fail(result.data.error_msg);
					        }
						},
					);
				},
				//计算立即购买后的有邮费等信息
				getBuyNowdata: function () {
				  	let that = this;
					var url = "/addons/litestore/api.order/buyNow"
					util.post(url,{
							goods_id: that.forpagedata.goods_id,
					        goods_num: that.forpagedata.goods_num,
					        goods_sku_id: that.forpagedata.goods_sku_id,
						}, 
						function (result) {
							that.goods_list = result.data.goods_list
							that.order_total_num = result.data.order_total_num
							that.order_total_price = result.data.order_total_price
							that.address = result.data.address
							that.express_price = result.data.express_price
							that.order_pay_price = result.data.order_pay_price

							that.has_error = result.data.has_error
							that.error_msg = result.data.error_msg
							//提示下架的商品
					        if (result.data.error_msg) {
					        	that.$toast.fail(result.data.error_msg);
					        }
						},
					);
				},
				MangerAdress(){
					this.$router.push({path: 'cartcheck/adresslist'})
				},
				onSubmit(){
					//提交订单
					let that = this;

				    if (that.goods_list.length==0) {
				      that.$toast.fail('此订单无商品');
				      return false;
				    }

				    if (that.disabled) {
				      return false;
				    }

				    if (that.has_error) {
				      that.$toast.fail(that.error_msg);
				      return false;
				    }
				    that.disabled = true;

				    if(this.$route.query.type=='cart'){
						//提交订单
						var url = "/addons/litestore/api.order/cart_pay"
						util.get(url,{type:'gzh'}, 
							function (result) {
					        	console.log(result);
								that.disabled = false;
								if(result.code==1008){
									//这里不直接跳转了，提示框  为绑定微信，跳转到my页面 绑定微信
									that.$dialog.confirm({
									  title: '未通过微信登录',
									  message: '您的账号未绑定微信号，请跳转后绑定微信号再进入未支付订单继续支付。'
									}).then(() => {
									  that.$router.push({path: '../my'})
									}).catch(() => {
									  that.$router.push({path: '../my/myorder'})
									});
								}else{
									//这里发起支付
									console.log('这里能够符合支付条件');
					        		that.wx_pay_fun(result.data);
								}
							},
							function (result) {
					        	console.log(result);
					      	},
					      	function () {
					      		that.disabled = false;
					      	},
						);
					}
					if(this.$route.query.type=='buyNow'){
						//提交订单
						var url = "/addons/litestore/api.order/buyNow_pay"
						util.post(url,{type:'gzh',
										goods_id: that.$route.query.goods_id,
								        goods_num: that.$route.query.goods_num,
								        goods_sku_id: that.$route.query.goods_sku_id,}, 
							function (result) {
					        	console.log(result);
								that.disabled = false;
								if(result.code==1008){
									//这里不直接跳转了，提示框  为绑定微信，跳转到my页面 绑定微信
									that.$dialog.confirm({
									  title: '未通过微信登录',
									  message: '您的账号未绑定微信号，请跳转后绑定微信号再进入未支付订单继续支付。'
									}).then(() => {
									  that.$router.push({path: '../my'})
									}).catch(() => {
									  that.$router.push({path: '../my/myorder'})
									});
								}else{
									//这里发起支付
									console.log('这里能够符合支付条件');
					        		that.wx_pay_fun(result.data);
								}
							},
							function (result) {
					        	console.log(result);
					      	},
					      	function () {
					      		that.disabled = false;
					      	},
						);
					}
				},
				wx_pay_fun(re){
					var vm = this;
					if (typeof WeixinJSBridge == "undefined"){//微信浏览器内置对象。参考微信官方文档
					  vm.$toast('请使用微信打开');
					  vm.$router.push({path: '../my/myorder'})
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
						vm.$router.push({path: '../my/myorder'})
					  }
					);
				}
		}
	}
</script>

<style>
.catcheck{
    height: 100%;
    position: absolute;
    width: 100%;
    top: 0;
    z-index: 18;
}
.catcheck .cube-page >.wrapper .content {
    margin: 0;
}
.catindex .catcheck .card-root-c {
    padding-top: 0px;
    position: relative;
}
.catcheck .cube-page >.wrapper{
	background-color: #edeff4;
}
.catcheck .van-stepper__input[disabled] {
	color:white!important;
	background-color:#6d189e!important;
}
.catcheck .goods_err_div{
  position:absolute;
  z-index:9;
  font-size:2em;
  line-height:100px;
  text-align:center;
  width:100%;
  color: #6d189e;
  letter-spacing:1.2em;
  font-weight:bolder;
  text-indent : 3em;
}
.catindex .catcheck .van-submit-bar{
	bottom: 0;
}
.catcheck .pric-cell .van-cell__value span{
  color:#f44;
}
.catcheck .vcg-c{
  padding-top: 10px;
  margin-bottom: 108px;
}
.catcheck .tagadress
{
    margin-left: 1em;
}
.catcheck .lianxirentip{
  color: #6d189e;
}
.catcheck .ico-ad{
  font-size: 12px!important;
  margin-right:0.68em;
}
.catcheck .ab_mt{
  margin-top: -1px;
  position: absolute;
}
</style>