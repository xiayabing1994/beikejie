import Toast from '/res/vant/toast/toast';
App({
  //全局API地址
  Domain: 'https://liteshop.com',
  //Domain: 'https://faliteshop.217dan.com',
  api_url: '',
  /* 设置api地址 */
  setApiUrl: function () {
    this.api_url = this.Domain + '/addons/litestore/api.';
  },
  onLaunch: function () {
    this.setApiUrl();
    //如果需要一进入小程序就要求授权登录,可在这里发起调用
    this.check(function (ret) { 
      
    });
  },
  onShow: function (options) {
    let that = this;
    // 获取小程序基础信息
    that.getWxappBase(function (wxapp) {
      // 设置navbar标题、颜色
      that.wx_setcolor(wxapp);
    });
  },
  wx_setcolor: function (wxapp){
    wx.setNavigationBarColor({
      frontColor: wxapp.TopTextColor,
      backgroundColor: wxapp.BackgroundColor,
    })
  },
  /**  获取小程序基础信息 */
  getWxappBase: function (callback) {
    this._get('wxapp/base', {}, function (result) {
      // 记录小程序基础信息
      wx.setStorageSync('wxapp', result.data.wxapp);
      callback && callback(result.data.wxapp);
    }, false, false);
  },
  /**post请求*/
  _post: function (url, data, success, fail, complete) {
    wx.showNavigationBarLoading();
    let App = this;
    data.token = wx.getStorageSync('token');
    wx.request({
      url: App.api_url + url,
      header: {
        'content-type': 'application/x-www-form-urlencoded',
      },
      method: 'POST',
      data: data,
      success: function (res) {
        console.log(res);
        if (res.data.code === 401) {
          App.check(App._post(url, data, success, fail, complete));
          return false;
        } else if (res.data.code === 0) {
          App.showError(res.data.msg, function () {
            fail && fail(res);
          });
          return false;
        }
        success && success(res.data);
      },
      fail: function (res) {
        App.showError(res.errMsg, function () {
          fail && fail(res);
        });
      },
      complete: function (res) {
        wx.hideLoading();
        wx.hideNavigationBarLoading();
        complete && complete(res);
      }
    });
  },


  /**get请求*/
  _get: function (url, data, success, fail, complete) {
    wx.showNavigationBarLoading();
    let App = this;
    // 构造请求参数
    data = data || {};

    // 构造get请求
    let request = function () {
      data.token = wx.getStorageSync('token');
      wx.request({
        url: App.api_url + url,
        header: {
          'content-type': 'application/json'
        },
        data: data,
        success: function (res) {
          console.log(res);
          if (res.data.code === 401) {
            App.check(App._get(url, data, success, fail, complete));
            return false;
          } else if (res.data.code === 0) {
            App.showError(res.data.msg);
            return false;
          } else {
            success && success(res.data);
          }
        },
        fail: function (res) {
          // console.log(res);
          App.showError(res.errMsg, function () {
            fail && fail(res);
          });
        },
        complete: function (res) {
          wx.hideNavigationBarLoading();
          complete && complete(res);
        },
      });
    };
    request();
  },

  showError: function (msg, callback) {
    wx.showModal({
      title: '温馨提示',
      content: msg,
      showCancel: false,
      success: function (res) {
        callback && callback();
      }
    });
  },
  showSuccess: function (msg, callback) {
    Toast.success(msg);
    callback && (setTimeout(function () {
      callback();
    }, 800));
    /*
    wx.showToast({
      title: msg,
      icon: 'success',
      success: function () {
        callback && (setTimeout(function () {
          callback();
        }, 1500));
      }
    });
    */
  },
  getStorageSyncwxapp: function (cb) {
    var that = this;
    if (wx.getStorageSync('wxapp')) {
      typeof cb == "function" && cb(wx.getStorageSync('wxapp'));
    } else {
      //这里循环等待出数据
      setTimeout(function () { that.getStorageSyncwxapp(cb); }, 500);
    }
  },

  /* 关于登录 */
  //判断是否登录
  check: function (cb) {
    var that = this;
    if (this.globalData.userInfo) {
      typeof cb == "function" && cb(this.globalData.userInfo);
    } else {
      this.login(cb);
    }
  },
  Log_after_fun: function (cb) {
    var that = this;
    if (this.globalData.userInfo) {
      typeof cb == "function" && cb(this.globalData.userInfo);
    } else {
      //这里循环等待出数据
      setTimeout(function () { that.Log_after_fun(cb); }, 500);
    }
  },
  //登录
  login: function (cb) {
    var that = this;
    var token = wx.getStorageSync('token') || '';
    //调用登录接口
    wx.login({
      success: function (res) {
        if (res.code) {
          //发起网络请求
          wx.request({
            url: that.api_url + 'user/login_hawk',
            data: {
              code: res.code,
              token: token
            },
            method: 'post',
            header: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            success: function (lres) {
              console.log(lres);
              var response = lres.data
              if (response.code == 1) {
                that.globalData.userInfo = response.data.userInfo;
                wx.setStorageSync('token', response.data.userInfo.token);
                typeof cb == "function" && cb(that.globalData.userInfo);
              } else {
                wx.setStorageSync('token', '');
                console.log("用户登录失败");
                if (response.data.errcode == 40125 || response.data.errcode == 40013 ){
                  wx.showModal({
                    title: '用户登录失败',
                    content: '请检查您是否正确配置了后台的小程序ID、小程序密钥以及开发者工具的小程序ID。',
                    showCancel: false,
                    success: function (res) {
                      //that.login(cb);
                    }
                  });
                }else{
                  wx.showModal({
                    title: '用户登录失败',
                    content: '请检查您是否已经安装“第三方登录”插件，然后重试。',
                    showCancel: false,
                    success: function (res) {
                      //that.login(cb);
                    }
                  });
                }
              }
            }
          });
        } else {
          console.log("用户失败")
        }
      }
    });
  },
  globalData: {
    userInfo: null
  },

})