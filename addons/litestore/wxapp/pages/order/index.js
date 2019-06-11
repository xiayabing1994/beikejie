let App = getApp();
Page({

  data: {
    OrderList:[],
    active:0,
    isNoData:true,
  },


  onLoad: function (options) {
    let that = this;
    //页面启动后 调取首页的数据
    that.setData({
      wxapp: wx.getStorageSync('wxapp')
    });
    App.wx_setcolor(that.data.wxapp);

    if (options.showType){
      that.setData({
        active: options.showType
      });
    }
  },

  onShow: function () {
    //这里拖取 数据
    let that = this;
    App._get('order/my', {}, function (result) {
      console.log(result.data);
      //这里对状态 进行分类
      result.data.forEach(function(item, index, arr){
        if (item.pay_status == "20" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10" ){
          arr[index].showText="待发货";
          arr[index].showType ="success";
          arr[index].showactive = 2;
        }
        if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "10" && item.receipt_status == "10"){
          arr[index].showText = "待收货";
          arr[index].showType = "primary";
          arr[index].showactive = 3;
        }
        if (item.pay_status == "10" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10") {
          arr[index].showText = "待付款";
          arr[index].showType = "danger";
          arr[index].showactive = 1;
        }
        if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "30" && item.receipt_status == "20") {
          arr[index].showText = "已完成";
          arr[index].showType = "";
        }
      });

      that.setData({
        OrderList: result.data
      });

      that.check_is_noData();
    });

  },
  bt_url: function (e) {
    wx.navigateTo({
      url: './detail?id=' + e.currentTarget.dataset.id
    })
  },
  onChange: function (e) {
    console.log(e);
    let that = this;
    that.setData({
      active: e.detail.index
    });

    //这里计算当前订单列表的长度
    that.check_is_noData();
  },
  check_is_noData: function () {
    let that = this;
    var isnodata = true;
    that.data.OrderList.forEach(function (item, index, arr) {
      if (that.data.active == 0) {
        isnodata = false
      } else {
        if (item.showactive == that.data.active) {
          isnodata = false
        }
      }
    });
    that.setData({
      isNoData: isnodata
    });
  }
 })