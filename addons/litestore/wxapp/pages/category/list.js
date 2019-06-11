let App = getApp();
Page({

  data: {
    scrollHeight: null,
    list: {},
    page: 1,
    last_page:null,
    noList: true,
    no_more: false,
    sel_type:"normal",
    c_id: -1,//-1为 搜索名字
    title:''
  },

  onLoad: function (options) {
    let that = this;

    that.setData({
      c_id: options.cid || -1,
      title:options.name,
      is_rename: options.rename == 1
    });
    //页面启动后 调取首页的数据
    that.setData({
      wxapp: wx.getStorageSync('wxapp')
    });
    App.wx_setcolor(that.data.wxapp);
    wx.setNavigationBarTitle({
      title: that.data.title||'所有商品'
    })

    // 设置商品列表高度
    that.setListHeight();

    //拖数据
    that.getGoodsList(true);

  },
  bindgetdata: function () {
    console.log('拖动到底');
    if (this.data.page >= this.data.last_page) {
      this.setData({ no_more: true });
      return false;
    }
    this.getGoodsList(false, ++this.data.page);
  },
  getGoodsList: function (is_super, page) {
    let that = this;
    App._get('goods/category_list', { 
        id: that.data.c_id, page: page || 1, types: that.data.sel_type, 
        name: that.data.title,
      }, function (result) {
      let resultList = result.data.listdata
        , dataList = that.data.list;
      if (is_super === true || typeof dataList === 'undefined') {
        that.setData({ list: resultList, noList: resultList.length == 0, no_more: page || 1 >= result.data.pagedata.last_page});
      }else{
        that.setData({
          list: dataList.concat(resultList),
          noList:false,
          no_more: resultList.length == 0
        })
      }
      that.setData({
        last_page: result.data.pagedata.last_page
      })

      console.log(result);
    });
  },

  onShow: function () {

  },

  onChange: function (event) {
    let types = ['normal', 'sales', 'price'];
    let that = this;
    //console.log(types[event.detail]);
    this.setData({
      list: {},
      page: 1,
      sel_type: types[event.detail],
    }, function () {
      // 获取商品列表
      that.getGoodsList(true);
    });
  },
  setListHeight: function () {
    let that = this;
    wx.getSystemInfo({
      success: function (res) {
        that.setData({
          scrollHeight: res.windowHeight - 50
        });
      }
    });
  },

})