<template>
	<div class="mangeradressv">
		<cube-page type="mangeradress-view" title="收货地址管理">
			<div slot="content">
				<van-address-list
				  v-model="chosenAddressId"
				  :list="list"
				  @add="onAdd"
				  @edit="onEdit"
				  @select="onSelect"
				/>
			</div>
		</cube-page>
	</div>
</template>

<script>
	import CubePage from './TP/cube-page.vue'
	import * as util from '@/utils/network'
	import { Toast } from 'vant';
	
	export default {
		components: {
			CubePage,
		},
		data() {
			return {
				chosenAddressId: '1',
			    list: [],
			    backupadress:[],
			};
		},
		created: function () {
			//获得地址数据
			let that = this;
			var url = "/addons/litestore/api.adress/lists"
			util.get(url,{}, 
				function (result) {
					console.log(result.data)
					that.backupadress = result.data.list
					var adresslist = [];
					result.data.list.forEach(function (v, i){  
					    var tpad = {}
					    tpad.id = v.address_id
					    tpad.name = v.name
					    tpad.tel = v.phone
					    tpad.address = v.Area.province+v.Area.city+v.Area.region+v.detail
					    adresslist.push(tpad)
					    if(v.isdefault=='1'){
					    	that.chosenAddressId = v.address_id
					    	that.$root.eventHub.$emit('changeadress', v)
					    }
					});
					that.list = adresslist
				},
			);
		},
		methods: {
			onAdd() {
		      this.$router.push({path: 'adressedit'})
		    },

		    onEdit(item, index) {
		      this.$router.push({path: 'adressedit', query: {id: item.id }})
		    },

		    onSelect(item, index) {
		    	let that = this;
				var url = "/addons/litestore/api.adress/setdefault"
				util.post(url,{ id: item.id },
					function (result) {
						console.log(result)
						that.$root.eventHub.$emit('changeadress', that.backupadress[index])
						Toast('默认地址成功更换');
				});
		    }
		}
	}
</script>

<style>
.mangeradressv{
    height: 100%;
    position: absolute;
    width: 100%;
    top: 0;
    z-index: 28;
}
.mangeradressv .van-address-item .van-radio__icon--checked .van-icon{
	border-color: #6d189e;
    background-color: #6d189e;
}
.mangeradressv .van-button--danger{
	background-color: #6d189e;
    border: 1px solid #6d189e;
}
</style>
