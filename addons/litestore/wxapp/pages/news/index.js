var App = getApp();
let wxParse = require("../../res/wxParse/wxParse.js");
Page({
  data: {
    archivesInfo: { article: {} },
    api_url: App.Domain,
  },
  onLoad: function (options) {
    var that = this;
    //页面启动后 调取首页的数据
    that.setData({
      wxapp: wx.getStorageSync('wxapp')
    });
    App.wx_setcolor(that.data.wxapp);
    
    let tmpid = 0;
    if (options.id != undefined) {
      tmpid = options.id;
    }

    App._get('index/getnew', {
      new_id: tmpid
    }, function (result) {
        console.log(result);
      result.data.newdata.content = result.data.newdata.content.replace(/<img src="\/uploads\//ig, "<img src=\"" + that.data.api_url + "\/uploads\/");
      //console.log(result.data.detail.content);
      wxParse.wxParse('content', 'html', result.data.newdata.content, that, 0);
        that.setData({
          archivesInfo: result.data.newdata
        });
      }
    );
  }
})