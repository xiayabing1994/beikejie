import Vue from 'vue'
import App from './App.vue'
import Vant from 'vant';
import 'vant/lib/index.css';
import router from './router'
import { Lazyload } from 'vant';
Vue.use(Lazyload);

import store from './store'//引入store

import FastClick from 'fastclick'

FastClick.attach(document.body) //  hack the active pseudo-classes failure caused by -webkit-overflow-scrolling touch

Vue.use(Vant);

Vue.config.productionTip = false

//远程加载js的方式
Vue.component('remote-script', {
    render: function (createElement) {
        var self = this;
        return createElement('script', {
            attrs: {
                type: 'text/javascript',
                src: this.src
            },
            on: {
                load: function (event) {
                    self.$emit('load', event);
                },
                error: function (event) {
                    self.$emit('error', event);
                },
                readystatechange: function (event) {
                    if (this.readyState == 'complete') {
                        self.$emit('load', event);
                    }
                }
            }
        });
    },
    props: {
        src: {
            type: String,
            required: true
        }
    }
});

new Vue({
  router,
  store,
  render: h => h(App),
    data: {
        eventHub: new Vue() //的空vue对象。就可以使用 this.$root.eventHub 获取对象。 / bus接收事件
    }

}).$mount('#app')
