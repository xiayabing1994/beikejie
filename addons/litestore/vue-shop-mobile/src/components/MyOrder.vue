<template>
	<div class="myorder">
		<cube-page type="myorder-view" title="我的订单">
			<div slot="content">
				<van-tabs v-model="active " class="tab-margin" :color="gcfg.BackgroundColor" @change="OnChange">

				  <van-tab title="全部订单">
				    <div class="oneorder" v-for="(item, idx) in OrderList" :key="idx">
					    <van-cell-group
					      <van-cell :title="'订单编号：'+item.order_no">
					       <van-tag slot="right-icon" :type="item.showType">{{item.showText}}</van-tag>
					      </van-cell>
					      <van-cell is-link :to="'./myorder/detail?id='+item.id">
					        <div slot="title" class='tile_sp'>
					          <span v-for="(goodsitem, key) in item.goods" :key="key">
					            {{goodsitem.goods_name}} * {{goodsitem.total_num}} </br>
					          </span>
					        </div>
					      </van-cell>
					      <van-cell :title="'合计:￥'+item.pay_price+' 数量: '+item.goods.length" :label="item.creattime_text">
					        <div slot="right-icon">
					          <van-button size="small" class="bt-zs" :to="'./myorder/detail?id='+item.id">订单详情</van-button>
					        </div>
					      </van-cell>
					    </van-cell-group>
				    </div>
				  </van-tab>

				  <van-tab title="未付款">
				    <div class="oneorder" v-for="(item, idx) in OrderList" :key="idx" v-if="item.showType=='danger'">
					    <van-cell-group
					      <van-cell :title="'订单编号：'+item.order_no">
					       <van-tag slot="right-icon" :type="item.showType">{{item.showText}}</van-tag>
					      </van-cell>
					      <van-cell is-link :to="'./myorder/detail?id='+item.id">
					        <div slot="title" class='tile_sp'>
					          <span v-for="(goodsitem, key) in item.goods" :key="key">
					            {{goodsitem.goods_name}} * {{goodsitem.total_num}} </br>
					          </span>
					        </div>
					      </van-cell>
					      <van-cell :title="'合计:￥'+item.pay_price+' 数量: '+item.goods.length" :label="item.creattime_text">
					        <div slot="right-icon">
					          <van-button size="small" class="bt-zs" :to="'./myorder/detail?id='+item.id">订单详情</van-button>
					        </div>
					      </van-cell>
					    </van-cell-group>
				    </div>
				  </van-tab>

				  <van-tab title="待发货">
				    <div class="oneorder" v-for="(item, idx) in OrderList" :key="idx" v-if="item.showType=='success'">
					    <van-cell-group
					      <van-cell :title="'订单编号：'+item.order_no">
					       <van-tag slot="right-icon" :type="item.showType">{{item.showText}}</van-tag>
					      </van-cell>
					      <van-cell is-link :to="'./myorder/detail?id='+item.id">
					        <div slot="title" class='tile_sp'>
					          <span v-for="(goodsitem, key) in item.goods" :key="key">
					            {{goodsitem.goods_name}} * {{goodsitem.total_num}} </br>
					          </span>
					        </div>
					      </van-cell>
					      <van-cell :title="'合计:￥'+item.pay_price+' 数量: '+item.goods.length" :label="item.creattime_text">
					        <div slot="right-icon">
					          <van-button size="small" class="bt-zs" :to="'./myorder/detail?id='+item.id">订单详情</van-button>
					        </div>
					      </van-cell>
					    </van-cell-group>
				    </div>
				  </van-tab>

				  <van-tab title="待收货">
				    <div class="oneorder" v-for="(item, idx) in OrderList" :key="idx" v-if="item.showType=='primary'">
					    <van-cell-group
					      <van-cell :title="'订单编号：'+item.order_no">
					       <van-tag slot="right-icon" :type="item.showType">{{item.showText}}</van-tag>
					      </van-cell>
					      <van-cell is-link :to="'./myorder/detail?id='+item.id">
					        <div slot="title" class='tile_sp'>
					          <span v-for="(goodsitem, key) in item.goods" :key="key">
					            {{goodsitem.goods_name}} * {{goodsitem.total_num}} </br>
					          </span>
					        </div>
					      </van-cell>
					      <van-cell :title="'合计:￥'+item.pay_price+' 数量: '+item.goods.length" :label="item.creattime_text">
					        <div slot="right-icon">
					          <van-button size="small" class="bt-zs" :to="'./myorder/detail?id='+item.id">订单详情</van-button>
					        </div>
					      </van-cell>
					    </van-cell-group>
				    </div>
				  </van-tab>

				</van-tabs>

				<div v-if="isNoData" class="liteshop-notcont" style="margin-top:130px;">
				    <div class="img">
				       <img src="@/assets/no-data.png"/>
				    </div>
				    <span class="cont">订单空空如也</span>
				</div>
			</div>
		</cube-page>
		<cube-view></cube-view>
	</div>
</template>

<script>
	import CubeView from '@/components/TP/cube-view'
	import CubePage from './TP/cube-page.vue'
	import * as util from '@/utils/network'
	
	export default {
		components: {
			CubePage,
			CubeView,
		},
		data() {
			return {
				OrderList:[],
    			active:this.$route.query.showType,
    			isNoData:true,
    			gcfg:[]
			};
		},
		created: function () {
			this.get_my_order()
			this.gcfg = this.$store.getters.getGcfg
		},
		watch:{
		  "$store.state.gcfg": function(){
		  	this.gcfg = this.$store.getters.getGcfg
		  },
		  $route(to,from) {
		  	console.log(to)
		  	console.log(from)
		  	if(to.path=='/my/myorder' && from.path=='/my/myorder/detail'){
		  		this.get_my_order()
		  	}
		  },
		},
		methods: {
			get_my_order(){
				var url = "/addons/litestore/api.order/my"
				let that = this;
				
				//这里直接调用网络接口
				util.get(url,{},
					function (result) {
							console.log(result.data);
							//这里对状态 进行分类
					      result.data.forEach(function(item, index, arr){
					        if (item.pay_status == "20" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10" ){
					          arr[index].showText="待发货";
					          arr[index].showType ="success";
					          arr[index].showactive = 2;
					        }
					        if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "10" && item.receipt_status == "10"){
					          arr[index].showText = "待收货";
					          arr[index].showType = "primary";
					          arr[index].showactive = 3;
					        }
					        if (item.pay_status == "10" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10") {
					          arr[index].showText = "待付款";
					          arr[index].showType = "danger";
					          arr[index].showactive = 1;
					        }
					        if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "30" && item.receipt_status == "20") {
					          arr[index].showText = "已完成";
					          arr[index].showType = "";
					        }
					      });

					      that.OrderList = result.data
					      that.check_is_noData();
					},
				);
			},
			check_is_noData(){
			    let that = this;
			    var isnodata = true;
			    that.OrderList.forEach(function (item, index, arr) {
			      if (that.active == 0) {
			        isnodata = false
			      } else {
			        if (item.showactive == that.active) {
			          isnodata = false
			        }
			      }
			    });
			    that.isNoData = isnodata
			},
			OnChange(){
				let that = this;
				that.check_is_noData();
			}
		}
	}
</script>

<style>
.myorder{
    height: 100%;
    position: absolute;
    width: 100%;
    top: 0;
    z-index: 28;
}
.myorder .cube-page >.wrapper .content {
    margin: 0;
}
.myorder .bt-zs {
	background-color:#6d189e!important;
	color:white!important;
}

.myorder .tile_sp{
  color:#999;
}
.myorder .right-padding{
  margin-right: 0.6em;
}
.myorder .oneorder{
  margin-top:0.3em;
  box-shadow: 0 1px 2px #dfb3f8;
}
.liteshop-notcont {
  margin:130px 100px;
}
.myorder .liteshop-notcont .img {
    width: 180px;
    height: 120px;
    margin: 0 auto;
}
.myorder .liteshop-notcont .img image {
  width: 100%;
  height: 100%;
}
.myorder .liteshop-notcont .cont {
  display: block;
  text-align: center;
  font-size: 16px;
  color: #6d189e;
  margin-top: 60px;
}
</style>
