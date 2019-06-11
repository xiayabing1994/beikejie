<template>
	<div>
		<van-search placeholder="请输入搜索关键词" v-model="re_value" @search="onSearch" show-action>
			<div slot="action" @click="onSearch">搜索</div>
		</van-search>

		<div style="bottom: 50px;display: flex;position: absolute;top: 54px;flex-direction: row;width: 100%;">
			<div style="background-color: #f8f8f8;width: 8em; overflow: scroll;">
				<van-badge-group style="" :active-key="activeKey" @change="onChange">
					<van-badge v-for="(rd, index) in recommendData" :key="index" :title="rd.name" :info="rd.childlist.length"/>
				</van-badge-group>
			</div>
			<div style="width: 100%; overflow: scroll;" v-if="showdata">
				<van-row gutter="3">
					<van-col span="8" v-for="(rd, index) in recommendData[activeKey].childlist" :key="index">
						<router-link :to="'./pageList/relist?cid='+rd.id+'&name='+rd.name">
							<div class="cate-img" :id="rd.id">
								<div style="width: 100%;height: 0;padding-bottom: 100%;position: relative;">
		                      		<img style="width: 100%;height: 100%;position: absolute;" :src="rd.ImageFrist"></img>
								</div>
		                      <div :style="'font-size: 0.8em;margin: 0px 0 2px;text-align: center;color:'+ gcfg.BackgroundColor+'!important;' ">{{rd.name}}</div>
		                    </div>
						</router-link>
					</van-col>
				</van-row>
			</div>
				
		</div>
				
		<BottomTabbar :curactive="1" v-on:setgcfg="setgcfg($event)"></BottomTabbar>
		<cube-view></cube-view>
	</div>
</template>

<script>
	import CubeView from '@/components/TP/cube-view'
	import BottomTabbar from '@/components/TP/BottomTabbar'
	import * as util from '@/utils/network'
	
	export default {
		components: {
			CubeView,
			BottomTabbar
		},
		data() {
			return {
				activeKey: 0,
				recommendData: [],
				showdata:false,
				re_value:'',
				gcfg:[],
			};
		},
		created: function () {
			this.axios_Request();
		},
		methods: {
			onSearch(){
				//跳转
				let that = this
				this.$router.push({path: './pageList/relist', query: {name: that.re_value }})
			},
			onChange(key) {
			  this.activeKey = key;
			},
			axios_Request: function() {
				var url = "/addons/litestore/api.category/Showlist"
				let that = this;
			
				//这里直接调用网络接口
				util.get(url, {},
					function(result) {
						that.recommendData = result['data']['categorydata']
						that.showdata = true
					},
				);
			},
		    setgcfg: function(res){
		       this.gcfg = res;
		    }
		}
	}
</script>

<style>
.van-badge--select{
	border-color: #6d189e;
}
.van-info{
	background-color: #6d189e;
}
.van-card__desc{
	max-height: 34px;
	white-space: inherit;
}
.van-card__thumb{
    top: 10px;
}
</style>
