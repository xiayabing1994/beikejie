<template>
	<div class="page_goods">
	<cube-page type="Goods-view" title="商品详情">
		<div slot="content">
			<van-swipe :autoplay="3000" :indicator-color="gcfg.BackgroundColor">
			  <van-swipe-item v-for="(image, index) in detail.imgs_url" :key="index">
			    <img style="width:100%;height:100%;" v-lazy="image" />
			  </van-swipe-item>
			</van-swipe>

			<van-cell-group>
			  <van-cell :title="detail.goods_name" :border="false" style="font-size:1.2em;margin-bottom:-0.5em;font-weight: bold;"/>
			   <van-cell value="" title-class='titlec'>
			    <template slot="icon">
			     <van-icon name="discount" :color="gcfg.BackgroundColor" size="1.8em" style="margin-right:0.3em;"/>
			    </template>
			    <template slot="title">
			      <span class="van-cell-text" >特价：</span>
			      <van-tag mark size="large" type="danger">￥{{goods_price}}</van-tag>
			    </template>
			    <template v-if="line_price!=0.00">
			      <span class="van-cell-text yuanjia" >原价：￥{{line_price}}</span>
			    </template>
			  </van-cell>
			  <van-cell :title="'库存：'+stock_num" :value="'销量:'+detail.goods_sales" />
		    </van-cell-group>

		    <div class="modal_cont_box">
			  <div class="til_conview">
			    <van-row class='in-title'>
			      <van-col><van-icon name="coupon" :color="gcfg.BackgroundColor" size="2em"/></van-col>
			      <van-col><span class="til_con"> 规格数量 </span></van-col>
			    </van-row>
			  </div>
			  <div v-if="specData!=null">
				  <div class="tmall-types" v-for="(attr, attr_idx) in specData.spec_attr" :key="attr_idx">
				    <div class="tipstxt">{{attr.group_name}}：</div>
				      <van-radio-group v-model="goods_spec_arr[attr_idx]" class="radio_c" :data-id="attr.group_id" @change="RonChange($event,attr_idx)">
				        <van-radio v-for="(item, item_idx) in attr.spec_items" :key="item_idx" :disabled="item.hidden" :checked-color="gcfg.BackgroundColor"
				        class="radio_cc" :name="item.item_id">{{item.spec_value}}</van-radio>
				      </van-radio-group>
				  </div>
			  </div>
			  <div class="tmall-types" style="display: inline-flex;">
				  <div class="tipstxt" style="margin-bottom: 0;line-height: 31px;">购买数量：</div>
				  <van-stepper disable-input integer v-model="goods_num" integer  :min="1" :max="999" />
			  </div>
			</div>

			<div class="modal_cont_box">
			  <div class="til_conview">
			    <van-row class='in-title'>
			      <van-col><van-icon name="column" :color="gcfg.BackgroundColor" size="2em"/></van-col>
			      <van-col><span class="til_con"> 商品描述 </span></van-col>
			    </van-row>
			  </div>
			  <div class="goods-cont-li">
			  	<div v-html="detail.content"></div>
	          </div>

				<Copyright style="margin-bottom:4em"></Copyright>

			</div>

			<van-goods-action>
			  <van-goods-action-mini-btn icon="cart" text="购物车" :info="cartnum==0?'':cartnum" link-type="switchTab" to="/CartIndex"/>
			  <van-goods-action-mini-btn icon="shop" link-type="switchTab" to="/" text="店铺" />
			  <van-goods-action-big-btn :loading="addcart_loading" text="加入购物车" @click="addcart"/>
			  <van-goods-action-big-btn text="立即购买" primary @click="ByNow"/>
			</van-goods-action>
		</div>
	</cube-page>
	</div>
</template>

<script>
	import CubePage from './TP/cube-page.vue'
	import Copyright from '@/components/TP/Copyright'
	import * as util from '@/utils/network'
	
	import { Toast } from 'vant';

	export default {
		data() {
			return {
				id:this.$route.query.id,
    			detail:[],
				gcfg:[],
				cartnum:0,
				addcart_loading:false,

			    specData:[],
				goods_sku_id: 0, // 规格id
			    goods_price:0,
			    stock_num:0,
			    line_price:0,

			    goods_num:1,//购买数量

			    goods_spec_arr: [], // 记录规格的数组
			    sku_hidden_arr:[],	// 记录不显示的sku

			}
		},
		components: {
			CubePage,
			Copyright,
		},
		methods:{
			addcart:function(){
				let that = this;
				that.addcart_loading=true
				var url = "/addons/litestore/api.cart/add"
				util.post(url,{
							   goods_id: that.id,
							   goods_num: that.goods_num,
							   goods_sku_id: that.goods_sku_id,
							 }, 
						function (result) {
							console.log(result.data);
							Toast.success(result.msg);
							that.addcart_loading=false
							that.cartnum=result.data.cart_total_num
						},
				);
			},
			ByNow:function(){
				let that = this;
				this.$router.push({path: '/CartIndex/cartcheck', query: {
					type: 'buyNow',
					goods_id: that.id,
			        goods_num: that.goods_num,
			        goods_sku_id: that.goods_sku_id,
				}})
			},
			axios_Request: function() {
				var url = "/addons/litestore/api.goods/detail"
				let that = this;
				
				//这里直接调用网络接口
				util.get(url,{'goods_id':that.id},
					function (result) {
						console.log(result.data);

						// 初始化商品多规格
					    if (result.data.detail.spec_type === '20') {
					      that.initManySpecData(result.data);
					    }else{
					      that.goods_sku_id=result.data.detail.spec[0].spec_sku_id
					      that.goods_price=result.data.detail.spec[0].goods_price
					      that.line_price=result.data.detail.spec[0].line_price
					      that.stock_num=result.data.detail.spec[0].stock_num
					    }

					    //根据选择后的情况 分配sku的可选情况
					    if (result.data.detail.spec_type === '20') {
					      that.make_sku_showData(result.data.specData,0);
					    }

						that.detail = result.data.detail
						that.specData = result.data.specData
					},
				);
			},
			/**
		     * 初始化商品多规格
		     */
		  initManySpecData: function (data) {
		    var that = this;
		    for (let i in data.specData.spec_list) {
		      if (data.specData.spec_list[i].form.stock_num >= 0){
		        var sku_id = data.specData.spec_list[i].spec_sku_id.split('_');
		        //初始化 sku 显示
		        //商品价格/划线价/库存
		        that.goods_sku_id=data.detail.spec[i].spec_sku_id
		        that.goods_price=data.detail.spec[i].goods_price
		        that.line_price=data.detail.spec[i].line_price
		        that.stock_num=data.detail.spec[i].stock_num

		        for (let j in sku_id) {
		          that.goods_spec_arr[j] = parseInt(sku_id[j]);
		        }
		        break;
		      }
		    }
		    //初始化 影藏sku数组
		    that.sku_hidden_arr = [];
		    for (let i in data.specData.spec_list) {
		      if (data.specData.spec_list[i].form.stock_num < 0) {
		        that.sku_hidden_arr.push(data.specData.spec_list[i].spec_sku_id.split('_'));
		      }
		    }
		  },
		  RonChange: function (e,attr_idx) {
		    let goods_spec_arr = this.goods_spec_arr

		    //这里如果发现目前选项是不可选的，那么通过分配其余可选的选项  
		    this.make_good_sel_sku(goods_spec_arr, attr_idx);

		    this.updateSpecGoods();
		    this.make_sku_showData(this.specData,attr_idx);
		  },
		  make_good_sel_sku: function (goods_spec_arr, attr_idx) {
		    var that = this;
		    //首先判断此选项是否合法
		    if (that.check_good_sel_sku(goods_spec_arr)){

		    }else{
		      //循环sku列表 找到当前选择的第一匹配sku项目
		      var spec_list = this.specData.spec_list;
		      spec_list.forEach(function (value, index, array) {
		        if (value.form.stock_num >= 0) {
		          var sku_id_arr = value.spec_sku_id.split('_');
		          sku_id_arr.forEach(function (sku_id_arrvalue, sku_id_arrindex, sku_id_arrarray) {
		            if (sku_id_arrindex == attr_idx && goods_spec_arr[sku_id_arrindex] == sku_id_arrvalue){
		              //找到目前的匹配项 可使用的sku
		              goods_spec_arr = sku_id_arr;
		            }
		          });
		        }
		      });
		    }
		    //格式化
		    goods_spec_arr.forEach(function (value, index, array) {
		      goods_spec_arr[index] = parseInt(value);
		    });

		    that.goods_spec_arr=goods_spec_arr
		  },
		  check_good_sel_sku: function (goods_spec_arr) {
		    var re_r = true;
		    //影藏sku组合情况：
		    var Sku_hidden = this.sku_hidden_arr;
		    Sku_hidden.forEach(function (Sku_hiddenvalue, Sku_hiddenindex, Sku_hiddenarray) {
		      //针对每个影藏sku 匹配
		      var peiduiNum = 0;
		      Sku_hiddenvalue.forEach(function (value, index, array) {
		        if (value == goods_spec_arr[index]) {
		          peiduiNum++;
		        }
		      });
		      if (peiduiNum == Sku_hiddenvalue.length) {
		        //发现了不合法
		        re_r = false;
		      }
		    });
		    return re_r;
		  },
		  /** 更新商品规格信息  */
		  updateSpecGoods: function () {
		    let spec_sku_id = this.goods_spec_arr.join('_');

		    // 查找skuItem
		    let spec_list = this.specData.spec_list,
		      skuItem = spec_list.find((val) => {
		        return val.spec_sku_id == spec_sku_id;
		      });

		    // 记录goods_sku_id
		    // 更新商品价格、划线价、库存
		    if (typeof skuItem === 'object') {
		      this.goods_sku_id=skuItem.spec_sku_id
		      this.goods_price=skuItem.form.goods_price
		      this.line_price=skuItem.form.line_price
		      this.stock_num=skuItem.form.stock_num
		    }
		  },
		  make_sku_showData: function (data,break_num) {
		    var that = this;
		    //显示的sku数据为：
		    var Showskuiteam = data.spec_attr;
		    //初始化显示数据hidden为false
		    Showskuiteam.forEach(function (value, index, array) {
		      value.spec_items.forEach(function (value1, index1, array1) { value1.hidden = false; });
		    });

		    //循环 行规格 可选格式化，根据后面所有不变的sku规格
		    Showskuiteam.forEach(function (value, index, array) {
		      //这里 那一个选项
		      //if (index != break_num) {
		        that.for_eachsku_showData(Showskuiteam, index);
		      //}
		    });
		  },
		  for_eachsku_showData: function (Showskuiteam, ForNum) {
		    //影藏sku组合情况：
		    var Sku_hidden = this.sku_hidden_arr;
		    //现在选择的情况是：
		    var Nowselect = this.goods_spec_arr;

		    //循环 每行规格 可选格式化，根据后面所有不变的sku规格
		    Sku_hidden.forEach(function (Sku_hiddenvalue, Sku_hiddenindex, Sku_hiddenarray) {
		      //针对每个影藏sku 匹配
		      var peiduiNum = 0;
		      Sku_hiddenvalue.forEach(function (value, index, array) {
		        if (index != ForNum) {
		          if (value == Nowselect[index]) {
		            peiduiNum++;
		          }
		        }
		      });
		      if (peiduiNum == (Nowselect.length - 1)) {
		        //此时 此sku为影藏项目
		        Showskuiteam.forEach(function (Showskuiteamvalue, Showskuiteamindex, Showskuiteamarray) {
		          Showskuiteamvalue.spec_items.forEach(function (value1, index1, array1) {
		            if (value1.item_id == Sku_hiddenvalue[ForNum]) {
		              value1.hidden = true;
		            }
		          });
		        });
		      }
		    });
		  },
		},
		created: function () {
			this.axios_Request();
			this.gcfg = this.$store.getters.getGcfg
			//这里获得购物车数量
			var url = "/addons/litestore/api.cart/getTotalNum"
			let that = this;
			
			//这里直接调用网络接口
			util.get(url,{},
				function (result) {
					that.cartnum=result.data.cart_total_num
				},
			);
		},
		watch:{
		  "$store.state.gcfg": function(){
		  	this.gcfg = this.$store.getters.getGcfg
		  }
		},
	}
</script>

<style>
.page_goods{
    height: 100%;
    position: absolute;
    width: 100%;
    top: 0;
    z-index: 28;
}
.page_goods .cube-page >.wrapper .content {
    margin: 0px;
}
.page_goods .van-swipe-item{
	height: 100vw!important;
}
.page_goods .titlec{
  font-size: 1.2em;
  color:#6d189e;
  font-weight: bold;
}
.page_goods .yuanjia {
  color:#666;
  text-decoration:line-through;
  margin-left:1em;
}
.page_goods .modal_cont_box {
  padding:10px 12px;
  background-color:white;
  margin-top:1em;
}
.page_goods .til_conview {
  width:100%;
  border-bottom:1px solid #eee;
}
.page_goods .in-title{
  font-size:1em;
  color:#6d189e;
  border-bottom:2px solid #6d189e;
  width:10em;
  display:block;
  margin:0 auto;
  line-height:2em;
  padding:0em 0 0em 3em;
}
.page_goods .til_con {
  font-weight:bold;
  margin-left:0.2em;
  font-size:1.1em;
  margin-left:0.68em;
}
.page_goods .tmall-types{
  margin-top:1em;
}
.page_goods .tipstxt {
  font-size: 18px;
  color: rgb(88, 88, 88);
  margin-bottom: 5px;
}
.page_goods .radio_c{
  margin-left:-1em;
  padding-top:0.3em; 
}
.page_goods .radio_cc{
  display:inline;
  margin-left:1em;
}
.page_goods .van-stepper__input[disabled] {
  color:white!important;
  background-color:#6d189e!important;
  z-index:-9!important;
}
.page_goods .goods-cont-li img {
  max-width: 99%!important;
  height:auto;
  display: block;
  margin: 0 auto;
  margin-left: 0rpx;
}
</style>