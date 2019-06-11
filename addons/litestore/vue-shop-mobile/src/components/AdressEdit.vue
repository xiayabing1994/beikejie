<template>
	<div class="mangeradressv">
		<cube-page type="mangeradress-view" title="收货地址编辑">
			<div slot="content">
				<van-address-edit
				  :area-list="areaList"
				  :address-info="addressInfo"
				  show-delete
				  @save="onSave"
				  @delete="onDelete"
				/>
			</div>
		</cube-page>
	</div>
</template>

<script>
	import CubePage from './TP/cube-page.vue'
	import * as util from '@/utils/network'
	import { Toast } from 'vant'

	import AreaList from '@/assets/area'
	
	export default {
		components: {
			CubePage,
		},
		data() {
			return {
				areaList:AreaList,
				id:this.$route.query.id,
				addressInfo:{
					name:''
				}
			};
		},
		created: function () {
			//获得当前地址数据
			if(this.id==undefined){
				return
			}
			let that = this;
			var url = "/addons/litestore/api.adress/detail"
			util.get(url,{ id: that.id },
				function (result) {
					console.log(result)
					that.addressInfo.name = result.data.detail.name
					that.addressInfo.tel = result.data.detail.phone
					that.addressInfo.id = result.data.detail.address_id
					that.addressInfo.addressDetail = result.data.detail.detail

					that.addressInfo.province = result.data.rArea[0]
					that.addressInfo.city = result.data.rArea[1]
					that.addressInfo.county = result.data.rArea[2]

					that.addressInfo.areaCode = "110101"
					that.addressInfo.isDefault = result.data.detail.isdefault=='1'
					that.addressInfo.postalCode = ""
				},
			);
		},
		methods: {
			onSave(content) {
		      //去除 省会城市 多了 市的bug
		      var pr_temp = content.province
		      if (pr_temp.charAt(pr_temp.length - 1) == "市" ){
			      pr_temp = pr_temp.substr(0, pr_temp.length - 1); 
			  }
		      var AdressAllCont = pr_temp+','+content.city+','+content.county;
		      
		      let that = this;
		      var url,showtext;
		      if(that.id==undefined){
		      	url = "/addons/litestore/api.adress/add"
		      	showtext = "地址增加成功"
		      }else{
		      	url = "/addons/litestore/api.adress/edit"
		      	showtext = "地址修改成功"
		      }
			  
			  util.post(url,{
			  		  id:that.id,
					  region:AdressAllCont,
				      name:content.name,
				      phone:content.tel,
				      detail:content.addressDetail,
			  		}, 
					function (result) {
						Toast(showtext);
						that.$router.go(-1)
					},
			  );
		    },
		    onDelete() {
		    	let that = this;
		    	if(that.addressInfo.isDefault){
		    		Toast('默认地址无法删除');
		    		return;
		    	}
				var url = "/addons/litestore/api.adress/del"
				util.post(url,{ id: that.id },
					function (result) {
						console.log(result)
						Toast('地址删除成功');
						that.$router.go(-1)
				});
		    },
		}
	}
</script>

<style>

</style>
