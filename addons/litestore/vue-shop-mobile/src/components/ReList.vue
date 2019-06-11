<template>
	<div class="Relist">
		<cube-page type="New-view" :title="toptitle">
			<div slot="content">
				<van-tabbar v-model="sel_active" :fixed="false" @change="onChange">
				  <van-tabbar-item icon="youzan-shield">综合</van-tabbar-item>
				  <van-tabbar-item icon="hot" info="hot" >销量</van-tabbar-item>
				  <van-tabbar-item icon="gold-coin" info="低">价格</van-tabbar-item>
				</van-tabbar>

				<van-list
				  v-model="loading"
				  :finished="finished"
				  finished-text="没有更多了"
				  @load="onLoad"
				>

				 <van-card custom-class="van-card-root-c" v-for="item in list" :key="item.id"
			      :price="'¥'+item.goods_min_price"
			      :origin-price="'销量:'+item.goods_sales"
			      :title="item.goods_name"
			      :centered="true"
			      :lazy-load="true"
			      :thumb="item.image"
				  @click="togoods(item.id)" />
				</van-list>

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
			CubeView,
			CubePage
		},
		data() {
			return {
				sel_active:0,
				list: [],
			    loading: false,
			    finished: false,
			    toptitle:'',

			    page: 1,
			    last_page:null,
			    noList: true,
			    sel_type:"normal",
			    c_id: -1,//-1为 搜索名字
			    title:''
			};
		},
		created: function () {
			this.c_id = this.$route.query.cid || -1
      		this.title = this.$route.query.name || ''
      		this.toptitle = this.title||'所有商品'
			this.axios_Request(true);
		},
		methods: {
			togoods: function(id){
				this.$router.push({path: 'relist/goods', query: {id: id}})
			},
			onChange: function(){
				console.log(this.sel_active)
			 	let types = ['normal', 'sales', 'price'];
			    this.sel_type = types[this.sel_active]
				console.log(this.sel_type)
			    this.axios_Request(true);
			},
			onLoad(){
				if (this.page >= this.last_page &&this.last_page!=null) {
			      this.finished=true
			      return false;
			    }
			    this.axios_Request(false, ++this.page);
			},
			axios_Request: function(is_super, page) {
				var url = "/addons/litestore/api.goods/category_list"
				let that = this;
			
				//这里直接调用网络接口
				util.get(url, {id: that.c_id, page: page || 1, types: that.sel_type, name: that.title},
					function(result) {
						  that.loading = false
						  let resultList = result.data.listdata
					        , dataList = that.list;
					      if (is_super === true || typeof dataList === 'undefined') {
					        that.list = resultList
					        that.noList = resultList.length == 0
					        that.finished = page || 1 >= result.data.pagedata.last_page
					      }else{
					          that.list = dataList.concat(resultList)
					          that.noList = false
					          that.finished = resultList.length == 0
					      }
					      that.last_page = result.data.pagedata.last_page

					      console.log(result);
					},
				);
			}
		}
	}
</script>

<style>
.Relist{
    height: 100%;
    position: absolute;
    width: 100%;
    top: 0;
    z-index: 2;
}
.Relist .van-tabbar{
	height: 62px;
	margin-bottom: 10px;
}
.Relist .van-tabbar-item__icon{
	font-size: 22px;
}
.Relist .van-tabbar-item {
	color: #383838;
}
.Relist .cube-page >.wrapper .content {
    margin: 0px;
}
.Relist .cube-page{
    background-color: #f8f8f8;
}
.Relist .van-tabbar-item--active {
  color:#6d189e!important;
  font-weight:bolder;
}
.Relist .van-card__origin-price {
	text-decoration: blink;
}
.Relist .van-card__thumb {
    top:0;
}
.Relist .van-card {
	background-color: #ffffff;
}
</style>
