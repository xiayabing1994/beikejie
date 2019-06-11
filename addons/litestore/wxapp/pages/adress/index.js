let App = getApp();
Page({

  data: {
    adresslist:[],
  },

  onLoad: function (options) {

  },
  onReady: function () {
    
  },
  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    let that = this;
    App._get('adress/lists', {}, function (result) {
      that.setData({
        adresslist: result.data.list
      });

      //获得dialog组件
      that.SwipeCellList = that.selectAllComponents(".vscell");
      //这里对所有的 vscell 默认选取
      that.SwipeCellList.forEach(function (value, index, array) {
        //console.log(value.dataset.defult);
        if (value.dataset.defult == '1') {
          that.SwipeCellList[index].open("left");
        }
      });
      if (that.SwipeCellList[1]) {
        that.SwipeCellList[1].open("right");
      }
    });

    //时间段
    setTimeout(function (){
      that.SwipeCellList.forEach(function (value, index, array) {
        if (value.dataset.defult == '1') {
          that.SwipeCellList[index].close();
        }
      });
      if (that.SwipeCellList[1]) {
        that.SwipeCellList[1].close();
      }
    }, 800)
  },
  onclickItem: function (e) {
    console.log(e);
    let that = this;
    if (e.detail =="left"){
      var idx = e.target.dataset.id;
      this.data.adresslist.forEach(function (value, index, array) {
        if (index == idx){
          value.isdefault = "1";
          //设置默认项
          App._post('adress/setdefault', { id: value.address_id}, function (result) {
            App.showSuccess(result.msg);
          });

        }else{
          value.isdefault = "0";
        }
      });
      this.setData({
        adresslist: this.data.adresslist
      });
      return;
    }
    if (e.detail == "right"){
      var idx = e.target.dataset.id;
      if (this.data.adresslist.length <= 1){
        App.showError('至少保留一个收货地址。');
        return;
      }
      this.data.adresslist.every(function (value, index) {
        if (index == idx) {
          //如果删除的为默认项
          if (value.isdefault == "1"){
            App.showError('无法删除默认项。');
          } else {
            that.data.adresslist.splice(index, 1);
          //这里提交删除
            App._post('adress/del', { id: value.address_id }, function (result) {
              App.showSuccess(result.msg);
            });
          }
          return false;
        }
        return true;
      });

      this.setData({
        adresslist: this.data.adresslist
      });
      return;
    }
    if (e.detail == "cell") {
      //跳转到 编辑页面
      var idx = e.target.dataset.adressid;
      wx.navigateTo({
        url: './edit?id=' + idx
      })
    }
  },

  createAddress:function(){
    wx.navigateTo({
      url: './create'
    });
  },
})