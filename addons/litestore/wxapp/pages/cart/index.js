let App = getApp();
Page({

  data: {
    goods_list: [], // 商品列表
    order_total_num: 0,
    order_total_price: 0,
  },
  
  onLoad: function (options) {
  },
  onShow: function () {
    this.getCartList();
  },
  /** 购物车列表 */
  getCartList: function () {
    let that = this;
    App._get('cart/getlists', {}, function (result) {
      console.log(result);
      that.setData(result.data);
      //提示下架的商品
      if (result.data.error_msg != '') {
        //App.showError(result.data.error_msg);
      }
    });
  },

  onCloseItem: function (e) {
    console.log(e);
    if (e.detail !='right'){
      return;
    }
    var that = this;
    var goods_sku_id = e.currentTarget.dataset.goods_sku_id;
    var goods_id = e.currentTarget.dataset.id;
    wx.showModal({
      title: "提示",
      content: "您确定要移除当前商品吗?",
      success: function (e) {
        e.confirm && App._post('cart/delete', {
          goods_id: goods_id,
          goods_sku_id: goods_sku_id
        }, function (result) {
          that.getCartList();
          //把刚刚删除按钮的消去
          that.SwipeCellList = that.selectAllComponents(".vscell");
          that.SwipeCellList.forEach(function (value, index, array) {
            that.SwipeCellList[index].close();
          });
        });
      }
    });
  },
  onplus: function (e) {
    console.log(e);
    let that = this,
    index = e.currentTarget.dataset.id,
    goodsSkuId = e.currentTarget.dataset.goods_sku_id,
    goods = that.data.goods_list[index],
    order_total_price = that.data.order_total_price;
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    App._post('cart/add', {
      goods_id: goods.goods_id,
      goods_num: 1,
      goods_sku_id: goodsSkuId,
    }, function (result) {
      console.log(result);
      goods.total_num++;
      that.setData({
        ['goods_list[' + index + ']']: goods,
        order_total_price: that.mathadd(order_total_price, goods.goods_price)
      });
    });
  },
  mathadd: function (arg1, arg2) {
    return (Number(arg1) + Number(arg2)).toFixed(2);
  },
  onsub: function (e) {
    console.log(e);
    let that = this,
    index = e.currentTarget.dataset.id,
    goodsSkuId = e.currentTarget.dataset.goods_sku_id,
    goods = that.data.goods_list[index],
    order_total_price = that.data.order_total_price;
    wx.showLoading({
      title: '加载中',
      mask: true
    })
    App._post('cart/sub', {
      goods_id: goods.goods_id,
      goods_sku_id: goodsSkuId,
    }, function (result) {
      console.log(result);
      goods.total_num--;
      goods.total_num > 0 &&
        that.setData({
          ['goods_list[' + index + ']']: goods,
        order_total_price: that.mathsub(order_total_price, goods.goods_price)
        });
    });
  },
  mathsub: function (arg1, arg2) {
    return (Number(arg1) - Number(arg2)).toFixed(2);
  },


  onSubmit: function (e) {
    if (this.data.goods_list.length==0){
      App.showError('请添置您的购物车。');
      return;
    }
    wx.navigateTo({
      url: './checout?type=cart'
    })
  },
})