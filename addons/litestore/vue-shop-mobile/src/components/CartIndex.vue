<template>
	<div class="catindex">
		<div :style="'margin-bottom:'+margin_top">
		 <div class="card-root-c" v-for="(item, idx) in goods_list" :key="idx">
		    <van-swipe-cell :right-width="65" :data-id="item.goods_id" :data-goods_sku_id="item.goods_sku_id" :on-close="onCloseItem">
		      <div v-if="item.show_error != 0" class="goods_err_div">{{item.show_error_text}}</div>
		      <van-card :class="item.show_error == 0 ? '' : 'error_goods'" 
		      :price="item.goods_price"  :thumb-link="'#/cartindex/goods?id='+item.goods_id"
		      :title="item.goods_name" 
		      :desc="item.goods_sku.goods_attr ? item.goods_sku.goods_attr :'默认规格'"
		      :centered="true" :lazy-load="true" :thumb="item.goods_sku.spec_image==''? item.image:item.goods_sku.img_show">
			      <div slot="footer" v-if="item.show_error == 0">
			        <van-stepper disable-input async-change
			        class="van-stepper-c" :value="item.total_num" integer :min="1" :max="999" 
			        @plus="onplus(idx,item.goods_sku_id)" 
			        @minus="onsub(idx,item.goods_sku_id)" />
			      </div>
		      </van-card>
		      <div slot="right" class='delb'><span>删除</span></div>
		    </van-swipe-cell>
		  </div>
		</div>

		<div v-if="!goods_list.length" class="liteshop-notcont" style="margin-top:130px;">
		    <div class="img">
		      <img src="@/assets/no-data.png"/>
		    </div>
		    <span class="cont">购物车空空如也</span>
		</div>

		  <van-submit-bar
			  :price="order_total_price*100"
			  button-text="开始结算"
			  @submit="onSubmit"
			/>

		<BottomTabbar :curactive="2"></BottomTabbar>
		<cube-view></cube-view>
	</div>
</template>

<script>
	import CubeView from '@/components/TP/cube-view'
	import BottomTabbar from '@/components/TP/BottomTabbar'
	import * as util from '@/utils/network'
	import { Dialog } from 'vant';
	import { Toast } from 'vant';
	
	export default {
		components: {
			CubeView,
			BottomTabbar
		},
		data() {
			return {
				goods_list: [], // 商品列表
			    order_total_num: 0,
			    order_total_price: 0,
			    margin_top:"108px",
			};
		},
		created: function () {
			this.getCartList()
		},
		watch:{
			$route(to,from) {
				console.log(to)
				if(to.path=="/CartIndex/cartcheck"||to.path=="/cartindex/goods"){
					this.margin_top = '0px'
				}
				if(to.path=="/CartIndex"){
					this.margin_top = '108px'
				}
		  },
		},
		methods: {
				onSubmit(){
					if (this.goods_list.length==0){
				      Toast.fail('请添置您的购物车。');
				      return;
				    }
				    this.$router.push({path: 'CartIndex/cartcheck', query: {type: 'cart'}})
				},
			  	/** 购物车列表 */
				getCartList: function () {
				  	let that = this;
					var url = "/addons/litestore/api.cart/getlists"
					util.get(url,{}, 
						function (result) {
							that.goods_list = result.data.goods_list
							that.order_total_num = result.data.order_total_num
							that.order_total_price = result.data.order_total_price
						},
					);
				},
				onCloseItem(clickPosition, instance) {
					let that = this;
				      switch (clickPosition) {
				        case 'left':
				        case 'cell':
				        case 'outside':
				          instance.close();
				          break;
				        case 'right':
				          Dialog.confirm({
				            message: '确定删除吗？'
				          }).then(() => {
				            instance.close();

				            var url = "/addons/litestore/api.cart/delete"
							util.get(url,{
								goods_id: instance.$attrs['data-id'],
						        goods_sku_id: instance.$attrs['data-goods_sku_id'],
							}, 
							  function (result) {
							  	console.log(result)
							  	//that.goods_list.splice(instance.$attrs['idx'],1);
							  	that.getCartList()
							  },
							);

				          }).catch(() => {});
				          break;
				      }
				},
				onplus: function (index,goodsSkuId) {
    				let that = this;
					let goods = that.goods_list[index],
    				order_total_price = that.order_total_price;

					var url = "/addons/litestore/api.cart/add"
					util.get(url,{
						goods_id: goods.goods_id,
				        goods_num: 1,
				        goods_sku_id: goodsSkuId,
					}, 
					  function (result) {
							goods.total_num++;
						    that.goods_list[index]=goods
						    that.order_total_price=that.mathadd(order_total_price, goods.goods_price)
					  },
					);
				},
				mathadd: function (arg1, arg2) {
				    return (Number(arg1) + Number(arg2)).toFixed(2);
				},
				onsub: function (index,goodsSkuId) {
					let that = this;
					let goods = that.goods_list[index],
    				order_total_price = that.order_total_price;

					var url = "/addons/litestore/api.cart/sub"
					util.get(url,{
						goods_id: goods.goods_id,
				        goods_sku_id: goodsSkuId,
					}, 
					  function (result) {
							goods.total_num--;
							if(goods.total_num > 0) {
						    	that.goods_list[index]=goods
						    	that.order_total_price=that.mathsub(order_total_price, goods.goods_price)
							}
					  },
					);
				    
				},
				mathsub: function (arg1, arg2) {
				    return (Number(arg1) - Number(arg2)).toFixed(2);
				},
				togoods: function(id){
					this.$router.push({path: 'cartindex/goods', query: {id: id}})
				},
		}
	}
</script>

<style>
.catindex .error_goods{
	background-color:#fff;filter:Alpha(Opacity=30);opacity:0.3;
}
.catindex .card-root-c{
	padding-top: 10px;
}
.catindex .delb {
	height:100%;
	width:65px;
	text-align:center;
	font-size:1.2em;
	background-color:#6d189e;
	color:white;
	font-weight:bold;
	display:flex;
	justify-content:center;
	align-items:Center;
}
.catindex .van-stepper__input[disabled] {
	color:white!important;
	background-color:#6d189e!important;
}
.catindex .goods_err_div{
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
.catindex .van-submit-bar{
	bottom: 49px;
	z-index: 8;
}
.catindex .van-card__footer{
	position: absolute;
    bottom: 10px;
    right: 6px;
}
.liteshop-notcont {
  margin:130px 100px;
}
.liteshop-notcont .img {
    width: 180px;
    height: 120px;
    margin: 0 auto;
}
.liteshop-notcont .img image {
  width: 100%;
  height: 100%;
}
.liteshop-notcont .cont {
  display: block;
  text-align: center;
  font-size: 16px;
  color: #6d189e;
  margin-top: 60px;
}
</style>
