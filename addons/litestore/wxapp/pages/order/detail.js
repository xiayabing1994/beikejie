var App = getApp();

Page({
  data: {
    wxapp: null,
    steps: [
      {
        text: '付款中',
        desc: '请及时支付'
      },
      {
        text: '待发货',
        desc: '后台配货中'
      },
      {
        text: '已发货',
        desc: '快递狂奔中'
      },
      {
        text: '已完成',
        desc: '享受宝物中'
      }
    ],
    active:0,
    id:null,
    detail: [],
    disabled: false,
  },

  onLoad: function (options) {
    var that = this;
    //页面启动后 调取首页的数据
    that.setData({
      wxapp:wx.getStorageSync('wxapp'),
      id: options.id
    });
    App.wx_setcolor(that.data.wxapp);
  },
  onShow: function () {
    var that = this;
    //获得订单信息
    App._get('order/detail', {
      id: that.data.id
    }, function (result) {
      let active = 0;
      //格式化商品状态
      var item = result.data.order;
      if (item.pay_status == "20" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10") {
        item.showText = "待发货";
        item.BTText = "已付款";
        item.BTtype = 'primary';
        active = 1;
      }
      if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "10" && item.receipt_status == "10") {
        item.showText = "待收货";
        item.BTText = "确认收货";
        item.BTtype = 'warning';
        active = 2;
      }
      if (item.pay_status == "10" && item.freight_status == "10" && item.order_status == "10" && item.receipt_status == "10") {
        item.showText = "待付款";
        item.BTText = "提交订单";
        item.BTtype = 'danger';
        active = 0;
      }
      if (item.pay_status == "20" && item.freight_status == "20" && item.order_status == "30" && item.receipt_status == "20") {
        item.showText = "已完成";
        item.BTText = "订单已完成";
        item.BTtype = 'default';
        active = 3;
      }
      that.setData({
        detail: item,
        active: active
      });
    });
  },
  TapCancel: function () {
    let that = this;
    wx.showModal({
      title: "提示",
      content: "确认取消订单？",
      success: function (o) {
        if (o.confirm) {
          App._post('order/cancel', { 'id': that.data.id }, function (result) {
            wx.navigateBack();
          });
        }
      }
    });
  },
  onClicktjButton: function () {
    let that = this;
    if (that.data.disabled) {
      return false;
    }
    that.data.disabled = true;

    wx.showLoading({
      title: '正在处理...'
    });

    //如果是确认收货
    if (that.data.active==2){
      App._post('order/finish', { 'id': that.data.id }, function (result) {
        console.log('success');
        wx.redirectTo({
          url: './detail?id=' + that.data.id
        })
      }, function (result) {
        console.log(result);
      }, function () {
        that.data.disabled = false;
      });
      return;
    }

    App._post('order/order_pay', { 'id': that.data.id}, function (result) {
      console.log('success');
      //这里发起支付
      that.wx_pay_fun(result.data);
    }, function (result) {
      console.log(result);
    }, function () {
      that.data.disabled = false;
    });
  },
  wx_pay_fun: function (Rdata) {
    let that = this;
    // 发起微信支付
    wx.requestPayment({
      'timeStamp': Rdata.timestamp,
      'nonceStr': Rdata.nonceStr,
      'package': Rdata.package,
      'signType': Rdata.signType,
      'paySign': Rdata.paySign,
      success: function (res) {
        console.log('支付成功');
        // 跳转到订单展示界面
        wx.redirectTo({
          url: './detail?id='+that.data.id
        })
      },
      fail: function (res) {
        console.log(res);
        App.showError('订单未支付', function () {
          // 跳转到未付款订单展示界面

        });
      },
    });
  }

})