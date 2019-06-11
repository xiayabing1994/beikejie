let App = getApp();
Page({

  data: {
    active: 0,
    category:[],
    scrollTop:0,
    api_url: App.Domain,
    re_value:'',
  },


  onLoad: function (options) {
    var that = this;
    //页面启动后 调取首页的数据
    that.setData({
      wxapp: wx.getStorageSync('wxapp')
    });
    App.wx_setcolor(that.data.wxapp);

    //获得分类数据
    App._get('category/Showlist', {}, function (result) {
      that.setData({
        category: result.data.categorydata
      })
      console.log(result);
    });
  },

  onShow: function () {

  },
  onChange: function(event) {
    this.setData({
      active: event.detail
    })
  },
  onSearchchange: function (event) {
    this.setData({
      re_value: event.detail
    })
  },
  onSearch: function (event) {
    console.log(this.data.re_value);
    //这里跳转到  搜索页
    wx.navigateTo({
      url: './list?rename=1&name=' + this.data.re_value
    })
  },
})