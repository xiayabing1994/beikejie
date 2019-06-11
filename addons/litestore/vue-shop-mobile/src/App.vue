<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
import * as util from '@/utils/network'

export default {
  created: function() {
      //在页面加载时读取localStorage里的状态信息
      if(localStorage.getItem("Gconfig")){
        this.$store.replaceState(
            Object.assign(this.$store.state,JSON.parse(localStorage.getItem("Gconfig")))
          );
      }
      //在页面刷新时将vuex里的信息保存到localStorage里
      window.addEventListener("beforeunload",()=>{
          localStorage.setItem("Gconfig",JSON.stringify(this.$store.state))
      })

      var url = "/addons/litestore/api.wxapp/base"
      let that = this;

      //这里直接调用网络接口
      util.get(url, { },
        function(result) {
          that.$store.commit('setCFG',result['data']['wxapp'])
        },
      );
    },
}
</script>

<style>
body {
  font-size: 16px;
  background-color: #fff;
  -webkit-font-smoothing: antialiased;
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
  margin-bottom: 50px;
}
</style>
