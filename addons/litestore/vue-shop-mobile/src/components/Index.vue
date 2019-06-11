<template>
	<div class='index'>
		<div style="position: absolute;top: 0;width: 100%;">
			<van-notice-bar :text="gcfg.Indexotice" left-icon="volume-o" mode="closeable" color="white" :background="gcfg.BackgroundColor" />

			<van-swipe :autoplay="3000" :indicator-color="gcfg.BackgroundColor">
				<van-swipe-item v-for="(image, index) in bannerlist" :key="index">
					<img class="topimg" @touchstart="touchEvent" @touchmove="touchEvent" @touchend="touchEvent" v-on:click="swipe_click(image.id)" :src="image.image" />
				</van-swipe-item>
			</van-swipe>

			<div v-show="isShow">
				<van-cell-group>
					<van-cell icon="star" title="New" label="最新上架" title-class="tc-sel" :style="'color:'+ gcfg.BackgroundColor+';' " label-class="lc-sel" />
				</van-cell-group>
				<div class="panel-body" style="width: 100%; overflow:scroll;">
					<div style="border: 1px  #000000;  margin: 0 13px 0;display: flex;">
						<div v-for="(rd, index) in newData" :key="index" style="margin: 0 1em 1em 0;">
							<router-link :to="'goods?id='+rd.goods_id">
								<img class="recommend-img" v-lazy="rd.ImageFrist" />
								<div class="onelist-hidden">{{rd.goods_name}}</div>
								<div class="price" :style="'color:'+ gcfg.BackgroundColor+'!important;' ">￥{{rd.spec[0].goods_price}}</div>
							</router-link>
						</div>
					</div>
				</div>

				<!-- 随机推荐 -->
				<van-cell-group>
					<van-cell icon="fire" title="Random" label="随机推荐" :style="'color:'+ gcfg.BackgroundColor+';' " title-class="tc-sel" label-class="lc-sel" />
				</van-cell-group>
				<div style="margin-bottom:18px;">
					<vue-waterfall-easy ref="waterfall" :imgsArr="randomlist" @click="clickFn" 
					@scrollReachBottom="getData" srcKey="ImageFrist" height="680" 
					:mobileGap="12" >
						<div class="img-info" slot-scope="props">
						  <p class="some-info">{{props.value.goods_name}}</p>
						  <p class="some-info" :style="'color:'+ gcfg.BackgroundColor+'!important;' ">￥{{props.value.spec[0].goods_price}}</p>
						</div>
						<div slot="waterfall-over"></div>
					</vue-waterfall-easy>
				</div>

				<Copyright></Copyright>
			</div>
		</div>


		<BottomTabbar :curactive="0"></BottomTabbar>
		<cube-view></cube-view>
	</div>
</template>

<script>
	import CubeView from '@/components/TP/cube-view'
	import BottomTabbar from '@/components/TP/BottomTabbar'
	import Copyright from '@/components/TP/Copyright'
	import * as util from '@/utils/network'

	import vueWaterfallEasy from 'vue-waterfall-easy'

	export default {
		name: 'app',
		components: {
			CubeView,
			BottomTabbar,
			Copyright,
			vueWaterfallEasy
		},
		data() {
			return {
				bannerlist: [],
				newData:[],
				randomlist: [],
				Indexotice: '',
				isShow:true,
				lastTouchModel:'',
				CanClick:false,
				gcfg:[],
			}
		},
		methods: {
			getData(){
				this.$refs.waterfall.waterfallOver()
			},
			clickFn(event, { index, value }) {
			  // 阻止a标签跳转
			  event.preventDefault()
			  // 只有当点击到图片时才进行操作
			  if (event.target.tagName.toLowerCase() == 'img') {
			    //console.log('img clicked',index, value)
			    this.$router.push({path: 'goods', query: {id: value.goods_id}})
			  }
			},
			touchEvent:function(e){
				if(this.lastTouchModel == 'touchstart' && e.type == 'touchend'){
					//此时为点击
					//console.log('touchclick')
					this.CanClick = true
				}
				this.lastTouchModel = e.type
			},
			swipe_click: function(imgid){
				if(this.CanClick){
					this.$router.push({path: 'news', query: {id: imgid }})
					this.CanClick = false
				}
			},
			axios_Request: function() {
				var url = "/addons/litestore/api.index/index"
				let that = this;

				//这里直接调用网络接口
				util.get(url, { },
					function(result) {
						that.bannerlist = result['data']['bannerlist']
						that.newData = result['data']['NewList']
						that.randomlist = result['data']['Randomlist']
					},
				);
			},
		},
		created: function() {
			this.axios_Request();
			if(this.$router.currentRoute.path=="/goods"||this.$router.currentRoute.path=="/news"){
				this.isShow = false
			}
			this.gcfg = this.$store.getters.getGcfg
		},
		watch:{
		  $route(to,from) {
			if((to.path=="/goods"||to.path=="/news")
				&& from.path=="/"){
				this.isShow = false
			}
			console.log(from);
			if((from.path=="/goods" || from.path=="/news")
				&& to.path=="/"){
				this.isShow = true
			}
		  },
		  "$store.state.gcfg": function(){
		  	this.gcfg = this.$store.getters.getGcfg
          },
		},
	}

</script>

<style>
	.index .img-info{
		padding: 6px 10px;
	}
	.index .some-info{
		margin: 0;
	    font-size: 0.9em;
	    text-align: center;
	}
	.index .price{
		text-align: center;
		margin-top: 0.2em;
	}
	.index .onelist-hidden{
		overflow:hidden;
		height: 1.2em;
		color:black;
		text-align: center;
	}
	.index .van-swipe-item img.topimg {
		display: block;
		height: 100%;
		width: 100%;
	}

	.index .lc-sel {
		color: black;
		font-size: 1.2em;
		font-weight: bolder;
		margin-left: -1.2em;
	}

	.index .recommend-img {
		height: auto;
		max-height: 8em;
	}

	.index .recommend-img:last-child {
		margin: 0 1em 0 0;
	}

	.index .img-group {
		position: relative;
		display: inline-block;
		width: 50%;
		margin-bottom: 13px;
	}

	.index .img-tip {
		position: absolute;
	    width: 66%;
	    height: 95%;
	    top: 3px;
	    margin-left: 5px;
		text-align: center;
	}
	.index .img-tip img{
		max-width: 100%;
		max-height: 98%;
		border-top-right-radius: 3em;
	}

	.index .img-group .img-tip span{
		color: white;
	    font-size: 0.68em;
	    width: 100%;
	    position: absolute;
	    left: 0;
	    bottom: 2px;
	    background-color: black;
	    opacity: 0.68;
		border-radius: 4px;
	}
	
	.index .newlist-c{
		display: flex;
		margin: 8px;
		flex-direction: row;
		flex-wrap: wrap;
	}

	.vue-waterfall-easy-container .vue-waterfall-easy a.img-inner-box {
	    box-shadow: 0 1px 3px rgb(200, 143, 232)!important;
	}

	[class*=van-hairline]::after {
	    border-bottom-width: 0!important;
	}
	
</style>

