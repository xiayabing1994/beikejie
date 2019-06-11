<template>
	<div class="page_news">
	<cube-page type="New-view" title="活动公告">
		<div slot="content">
			 <div class="cover"><img :src="NewsData.image"/></div>

			<van-panel :title="NewsData.title" :desc="NewsData.updatetime">
				<div class="con" v-html="NewsData.content"></div>
			</van-panel>
		</div>
	</cube-page>
	</div>
</template>


<script>
	import CubePage from './TP/cube-page.vue'
	import * as util from '@/utils/network'
	
	export default {
		data() {
			return {
				 id:this.$route.query.id,
				 NewsData:[]
			}
		},
		components: {
			CubePage
		},
		methods:{
			axios_Request: function() {
				var url = "/addons/litestore/api.index/getnew"
				let that = this;
				
				//这里直接调用网络接口
				util.get(url,{'new_id':that.id},
					function (result) {
						that.NewsData = result['data']['newdata']
					},
				);
			},
		},
		created: function () {
			this.axios_Request();
		}
	}
</script>

<style>
.page_news{
	height: 100%;
    position: absolute;
    width: 100%;
    top: 0;
    z-index: 28;
}
.page_news .cover {
}
.page_news img {
    max-width: 100%;
}
.page_news main.content {
    text-align: left;
}
.page_news .van-cell__label {
    text-align: right;
}
.page_news .con {
    padding-bottom: 0.8em;
}

.page_news .van-hairline--top-bottom::after {
    border-width: 0!important;
}
</style>