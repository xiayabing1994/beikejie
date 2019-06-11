import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);
 const state={   //要设置的全局访问的state对象
     gcfg:null
   };
const getters = {   //实时监听state值的变化(最新状态)
    getGcfg(){
    	return state.gcfg
    }
};
const mutations = {
    setCFG(state,cfg){
	   state.gcfg = cfg
    }
};
 const store = new Vuex.Store({
       state,
       getters,
       mutations
});
export default store;