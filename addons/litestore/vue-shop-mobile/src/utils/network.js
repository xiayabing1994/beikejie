import axios from 'axios'
import Qs from 'qs'
const config = require("../../WebConfig"); 
import { Dialog } from 'vant';

var ua = navigator.userAgent.toLowerCase();//获取判断用的对象
let IsWX = false
if (ua.match(/MicroMessenger/i) == "micromessenger") {
    IsWX = true
}

export function post(url,data,success,fail,complete){
	data.token = getCookie('token');
	console.log(config.ROOT+url);
	var req = axios.post(config.ROOT+url,Qs.stringify(data),{
		headers: {
			'Content-Type': 'application/x-www-form-urlencoded'
		}
	})
	.then(function(res){
		console.log(res);
		if(res.data.code==0){
			Dialog.alert({
			  title: '温馨提示',
			  message: res.data.msg
			}).then(() => {
			});
		}else{
			success && success(res.data);
		}
		complete && complete();
	})
	.catch(function(err){
		console.log(err)
		if(err.response) {
			if(err.response.data.code==401){
				//未登录
				if(IsWX)
					window.location.href=config.PROXYROOT+'/addons/litestore/api.uservue/connect?platform=wechat&url='+window.location.href; 
				else
				    window.location.href=config.ROOT+'/index/user/login'; 
			}
			console.log(err.response)
		}
		fail && fail(err);
		complete && complete();
	})
}


export function get(url,data,success,fail,complete){
	data.token = getCookie('token');
	console.log(config.ROOT+url);
	var req = axios.get(config.ROOT+url,{params:data})
	.then(function(res){
		console.log(res);
		if(res.data.code==0){
			Dialog.alert({
			  title: '温馨提示',
			  message: res.data.msg
			}).then(() => {
			});
		}else{
			success && success(res.data);
		}
		complete && complete();
	})
	.catch(function(err){
		console.log(err)
		if(err.response) {
			if(err.response.data.code==401){
				//未登录
				if(IsWX)
					window.location.href=config.PROXYROOT+'/addons/litestore/api.uservue/connect?platform=wechat&url='+window.location.href; 
				else
				    window.location.href=config.ROOT+'/index/user/login'; 
			}
			console.log(err.response)
		}
		fail && fail(err);
		complete && complete();
	})
}

function getCookie(c_name){
	//判断document.cookie对象里面是否存有cookie
	if (document.cookie.length>0){
		var c_start=document.cookie.indexOf(c_name + "=")
		//如果document.cookie对象里面有cookie则查找是否有指定的cookie，如果有则返回指定的cookie值，如果没有则返回空字符串
		if (c_start!=-1){ 
			c_start=c_start + c_name.length+1 
			var c_end=document.cookie.indexOf(";",c_start)
			if (c_end==-1) c_end=document.cookie.length
			return unescape(document.cookie.substring(c_start,c_end))
			} 
		}
	return ""
}