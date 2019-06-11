let App = getApp();
Page({

  data: {
    region:null,
    disabled:false,
    phoneNumber:""
  },

  onLoad: function (options) {

  },

  onShow: function () {

  },

  saveData: function (e) {
    let that = this;
    let values = e.detail.value;
    values.region = this.data.region;
    //去除 省会城市 多了 市的bug
    var pr_temp = values.region[0];
    if (pr_temp.charAt(pr_temp.length - 1) == "市") {
      pr_temp = pr_temp.substr(0, pr_temp.length - 1);
      values.region[0] = pr_temp;
    }
    //console.log(values);
    // 表单验证
    if (!that.validation(values)) {
      App.showError(that.data.error);
      return false;
    }
    // 按钮禁用
    that.setData({
      disabled: true
    });
    //这里提交服务器
    App._post('adress/add', values, function (result) {
     // console.log(result.msg);
      App.showSuccess(result.msg, function () {
        wx.navigateBack();
      });
    }, false, function () {
      that.setData({
        disabled: false
      });
    });
  },

  validation: function (values) {
    if (values.name === '') {
      this.data.error = '收件人不能为空';
      return false;
    }
    if (values.phone.length < 1) {
      this.data.error = '手机号不能为空';
      return false;
    }
    if (values.phone.length !== 11) {
      this.data.error = '手机号长度有误';
      return false;
    }
    let reg = /^(((13[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/;
    if (!reg.test(values.phone)) {
      this.data.error = '手机号不符合要求';
      return false;
    }
    if (!this.data.region) {
      this.data.error = '省市区不能空';
      return false;
    }
    if (values.detail === '') {
      this.data.error = '详细地址不能为空';
      return false;
    }
    return true;
  },
  bindRegionChange: function (e) {
    this.setData({
      region: e.detail.value
    })
  },
  getPhoneNumber: function (e) {
    if (e.detail.iv == undefined) {
      return;
    }
    let dataTMp = [];
    dataTMp.encryptedData = e.detail.encryptedData;
    dataTMp.iv = e.detail.iv;
    var that = this;
    App._post('aboutwechat/get_PhoneNum', dataTMp, function (result) {
      App.showSuccess('成功获取');
      that.setData({
        phoneNumber: result.data.phoneNumber
      });
    });
  },
})