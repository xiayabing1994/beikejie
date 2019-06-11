let App = getApp();
Page({
  data: {
    // banner轮播组件属性
    indicatorDots: true, // 是否显示面板指示点	
    autoplay: true, // 是否自动切换
    interval: 3000, // 自动切换时间间隔
    duration: 800, // 滑动动画时长
    wxapp:[],
    newlist:[],
    randomlist: [],
    banner:[],
  },
  onLoad: function (options) {
    //页面启动后 调取首页的数据
    let that = this;
    App.getStorageSyncwxapp(function (ret) {
      that.setData({
        wxapp: ret
      });
      wx.setNavigationBarTitle({
        title: ret.LiteName
      });
    });
  },
  onShow: function () {
    //这里获得最近的商品数据 随机商品数据
    let that = this;
    App._get('index/index', {}, function (result) {
        that.setData({
        newlist: result.data.NewList,
        randomlist: result.data.Randomlist,
        banner: result.data.bannerlist
      });
    });
  },
  onShareAppMessage: function () {
    return {
      title: "小程序商城首页",
      desc: "",
      path: "/pages/index/index"
    };
  }
})