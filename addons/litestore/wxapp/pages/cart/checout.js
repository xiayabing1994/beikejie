let App = getApp();
Page({

  data: {
    wxapp: [],
    fromtype:null,
    goods_list: [], // 商品列表
    order_total_num: 0,
    express_price:0,
    order_total_price: 0,
    order_pay_price:0,
    disabled: false,
    from_options:[],
  },

  onLoad: function (options) {
    let that = this;
    //页面启动后 调取首页的数据
    that.setData({
      wxapp: wx.getStorageSync('wxapp'),
      fromtype: options.type,
      from_options: options
    });
    App.wx_setcolor(that.data.wxapp);
  },

  onShow: function () {
    //这里根据类型  取出 数据
    let that = this;
    if (that.data.fromtype =='cart'){
      App._get('cart/getlists', {}, function (result) {
        console.log(result);
        that.setData(result.data);
        //提示下架的商品
        if (result.data.error_msg != '') {
          App.showError(result.data.error_msg);
        }
      });
    }
    if (that.data.fromtype == 'buyNow') {
      App._get('order/buyNow', {
        goods_id: that.data.from_options.goods_id,
        goods_num: that.data.from_options.goods_num,
        goods_sku_id: that.data.from_options.goods_sku_id,
      }, function (result) {
        console.log(result);
        that.setData(result.data);
        //提示下架的商品
        if (result.data.error_msg != null) {
          App.showError(result.data.error_msg);
        }
      });
    }
  },

  TapAdress: function () {
    wx.navigateTo({
      url: '../adress/index'
    });
  },
  onClicktjButton: function () {
    let that = this;

    if (that.data.goods_list.length==0) {
      App.showError('此订单无商品');
      return false;
    }

    if (that.data.disabled) {
      return false;
    }

    //这里开始提交订单
    console.log(that.data);
    if (that.data.has_error) {
      App.showError(that.data.error_msg);
      return false;
    }
    that.data.disabled = true;

    wx.showLoading({
      title: '正在处理...'
    });

    if (that.data.fromtype=='cart'){
      //提交订单
      App._post('order/cart_pay', {}, function (result) {
        console.log('success');
        //这里发起支付
        that.wx_pay_fun(result.data);
      }, function (result) {
        console.log(result);
      }, function () {
        that.data.disabled = false;
      });
    }
    if (that.data.fromtype == 'buyNow') {
      //提交订单
      App._post('order/buyNow_pay', {
        goods_id: that.data.from_options.goods_id,
        goods_num: that.data.from_options.goods_num,
        goods_sku_id: that.data.from_options.goods_sku_id
        }, function (result) {
        console.log('success');
        //这里发起支付
        that.wx_pay_fun(result.data);
      }, function (result) {
        console.log(result);
      }, function () {
        that.data.disabled = false;
      });
    }
  },

  wx_pay_fun: function (Rdata){
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
          url: '../order/index?showType=2'
        })
      },
      fail: function (res) {
        console.log(res);
        App.showError('订单未支付', function () {
          // 跳转到未付款订单展示界面
          wx.redirectTo({
            url: '../order/index?showType=1'
          })
        });
      },
    });
  }
})