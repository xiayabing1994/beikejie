let App = getApp();
const util = require('../../utils/util');
let wxParse = require("../../res/wxParse/wxParse.js");
Page({

  data: {
    // banner轮播组件属性
    indicatorDots: true, // 是否显示面板指示点	
    autoplay: true, // 是否自动切换
    interval: 3000, // 自动切换时间间隔
    duration: 800, // 滑动动画时长
    wxapp: [],
    detail:[],
    api_url: App.Domain,
    goods_spec_arr: [], // 记录规格的数组
    specData:[],
    goods_num:1,//购买数量
    goods_sku_id: 0, // 规格id
    goods_price:0,
    stock_num:0,
    line_price:0,
    cartnum:0,
    addcart_loading:false,

    current_img_index:0,
    sku_hidden_arr:[]
  },

  onLoad: function (options) {
    let that = this;

    //页面启动后 调取首页的数据
    that.setData({
      wxapp: wx.getStorageSync('wxapp')
    });
    App.wx_setcolor(that.data.wxapp);

    // 商品id
    that.data.goods_id = options.goods_id;
    // 获取商品信息
    App._get('goods/detail', {
      goods_id: that.data.goods_id
    }, function (result) {
      
      // 初始化商品多规格
      if (result.data.detail.spec_type === '20') {
        that.initManySpecData(result.data);
      }else{
        that.setData(
          {
            goods_sku_id: result.data.detail.spec[0].spec_sku_id,
            goods_price: result.data.detail.spec[0].goods_price,
            line_price: result.data.detail.spec[0].line_price,
            stock_num: result.data.detail.spec[0].stock_num
          }
        );
      }

      //根据选择后的情况 分配sku的可选情况
      if (result.data.detail.spec_type === '20') {
        that.make_sku_showData(result.data.specData,0);
      }

      //这里处理富文本
      if (result.data.detail.content.length > 0) {
        //首先把图片格式化
        result.data.detail.content = result.data.detail.content.replace(/<img src="\/uploads\//ig, "<img src=\"" + that.data.api_url +"\/uploads\/");
        wxParse.wxParse('content', 'html', result.data.detail.content, that, 0);
      }

      that.setData(
        {
          detail: result.data.detail,
          specData: result.data.specData,
          goods_spec_arr: that.data.goods_spec_arr,
        }
      );
      console.log(result);
    });

    //这里获得购物车数量
    App._get('cart/getTotalNum', {}, function (result) {
      that.setData( {
          cartnum: result.data.cart_total_num,
        } );
    });

  },

  onShow: function () {

  },

  make_sku_showData: function (data,break_num) {
    var that = this;
    //显示的sku数据为：
    var Showskuiteam = data.spec_attr;
    //初始化显示数据hidden为false
    Showskuiteam.forEach(function (value, index, array) {
      value.spec_items.forEach(function (value1, index1, array1) { value1.hidden = false; });
    });

    //循环 行规格 可选格式化，根据后面所有不变的sku规格
    Showskuiteam.forEach(function (value, index, array) {
      //这里 那一个选项
      //if (index != break_num) {
        that.for_eachsku_showData(Showskuiteam, index);
      //}
    });
  },

  for_eachsku_showData: function (Showskuiteam, ForNum) {
    //影藏sku组合情况：
    var Sku_hidden = this.data.sku_hidden_arr;
    //现在选择的情况是：
    var Nowselect = this.data.goods_spec_arr;

    //循环 每行规格 可选格式化，根据后面所有不变的sku规格
    Sku_hidden.forEach(function (Sku_hiddenvalue, Sku_hiddenindex, Sku_hiddenarray) {
      //针对每个影藏sku 匹配
      var peiduiNum = 0;
      Sku_hiddenvalue.forEach(function (value, index, array) {
        if (index != ForNum) {
          if (value == Nowselect[index]) {
            peiduiNum++;
          }
        }
      });
      if (peiduiNum == (Nowselect.length - 1)) {
        //此时 此sku为影藏项目
        Showskuiteam.forEach(function (Showskuiteamvalue, Showskuiteamindex, Showskuiteamarray) {
          Showskuiteamvalue.spec_items.forEach(function (value1, index1, array1) {
            if (value1.item_id == Sku_hiddenvalue[ForNum]) {
              value1.hidden = true;
            }
          });
        });
      }
    });
  },
  /**
     * 初始化商品多规格
     */
  initManySpecData: function (data) {
    var that = this;
    for (let i in data.specData.spec_list) {
      if (data.specData.spec_list[i].form.stock_num >= 0){
        var sku_id = data.specData.spec_list[i].spec_sku_id.split('_');
        //初始化 sku 显示
        // 商品价格/划线价/库存
        that.setData({
            goods_sku_id: data.detail.spec[i].spec_sku_id,
            goods_price: data.detail.spec[i].goods_price,
            line_price: data.detail.spec[i].line_price,
            stock_num: data.detail.spec[i].stock_num
        });
        for (let j in sku_id) {
          that.data.goods_spec_arr[j] = parseInt(sku_id[j]);
        }
        break;
      }
    }

    //初始化 影藏sku数组
    that.data.sku_hidden_arr = [];
    for (let i in data.specData.spec_list) {
      if (data.specData.spec_list[i].form.stock_num < 0) {
        that.data.sku_hidden_arr.push(data.specData.spec_list[i].spec_sku_id.split('_'));
      }
    }
  },
  check_good_sel_sku: function (goods_spec_arr) {
    var re_r = true;
    //影藏sku组合情况：
    var Sku_hidden = this.data.sku_hidden_arr;
    Sku_hidden.forEach(function (Sku_hiddenvalue, Sku_hiddenindex, Sku_hiddenarray) {
      //针对每个影藏sku 匹配
      var peiduiNum = 0;
      Sku_hiddenvalue.forEach(function (value, index, array) {
        if (value == goods_spec_arr[index]) {
          peiduiNum++;
        }
      });
      if (peiduiNum == Sku_hiddenvalue.length) {
        //发现了不合法
        re_r = false;
      }
    });
    return re_r;
  },
  make_good_sel_sku: function (goods_spec_arr, attr_idx) {
    var that = this;
    //首先判断此选项是否合法
    if (that.check_good_sel_sku(goods_spec_arr)){

    }else{
      //循环sku列表 找到当前选择的第一匹配sku项目
      var spec_list = this.data.specData.spec_list;
      spec_list.forEach(function (value, index, array) {
        if (value.form.stock_num >= 0) {
          var sku_id_arr = value.spec_sku_id.split('_');
          sku_id_arr.forEach(function (sku_id_arrvalue, sku_id_arrindex, sku_id_arrarray) {
            if (sku_id_arrindex == attr_idx && goods_spec_arr[sku_id_arrindex] == sku_id_arrvalue){
              //找到目前的匹配项 可使用的sku
              goods_spec_arr = sku_id_arr;
            }
          });
        }
      });
    }
    //格式化
    goods_spec_arr.forEach(function (value, index, array) {
      goods_spec_arr[index] = parseInt(value);
    });

    that.setData({
      goods_spec_arr: goods_spec_arr,
    });
  },
  RonChange: function (e) {
    console.log(e);
    let goods_spec_arr = this.data.goods_spec_arr;
    goods_spec_arr[e.currentTarget.dataset.attr_idx] = parseInt(e.detail);

    //这里如果发现目前选项是不可选的，那么通过分配其余可选的选项  
    this.make_good_sel_sku(goods_spec_arr, e.currentTarget.dataset.attr_idx);

    this.updateSpecGoods();
    this.make_sku_showData(this.data.specData, e.currentTarget.dataset.attr_idx);
    this.setData({
      specData: this.data.specData,
    });
   // console.log(e);
  },
  /** 更新商品规格信息  */
  updateSpecGoods: function () {
    let spec_sku_id = this.data.goods_spec_arr.join('_');

    // 查找skuItem
    let spec_list = this.data.specData.spec_list,
      skuItem = spec_list.find((val) => {
        return val.spec_sku_id == spec_sku_id;
      });

    // 记录goods_sku_id
    // 更新商品价格、划线价、库存
    if (typeof skuItem === 'object') {
      this.setData({
        goods_sku_id: skuItem.spec_sku_id,
        goods_price: skuItem.form.goods_price,
        line_price: skuItem.form.line_price,
        stock_num: skuItem.form.stock_num,
      });
    }

    //这里对 规格的封面图进行格式化
    //首张图片为
    var frist_img = skuItem.form.imgshow;
    if (frist_img === null || frist_img === ''){
      return;
    }
    var imgs_arr = this.data.detail.imgs_url;
    //查找有重复的 立即删掉
    for (let i in imgs_arr) {
      if (imgs_arr[i] == frist_img){
        imgs_arr.splice(i, 1);
      }
    }
    imgs_arr.unshift(frist_img);
    this.setData({
      'detail.imgs_url': imgs_arr,
      current_img_index:0,
    });
  },
  onstepChange: function (e) {
    //console.log(e);
    this.setData({
      goods_num: e.detail,
    });
  },
  Tap_topimg: function (e) {
    console.log(e);
    let that = this;
    let id = e.target.dataset.id;

    wx.previewImage({
      current: that.data.detail.imgs_url[id],
      urls: that.data.detail.imgs_url
    })
  },
  addcart: function (e) {
    let that = this;
    console.log(e);
    that.setData({
      addcart_loading: true
    });
    
    // 加入购物车
    App._post('cart/add', {
      goods_id: that.data.goods_id,
      goods_num: that.data.goods_num,
      goods_sku_id: that.data.goods_sku_id,
    }, function (result) {
      App.showSuccess(result.msg);
      that.setData({
        cartnum: result.data.cart_total_num,
      });
      }, null, function (result) {
        that.setData({
          addcart_loading: false
        });
      });
  },
  ByNow: function (e) {
    let that = this;
    wx.navigateTo({
      url: '../cart/checout?' + util.urlencode({
        type: 'buyNow',
        goods_id: that.data.goods_id,
        goods_num: that.data.goods_num,
        goods_sku_id: that.data.goods_sku_id,
      })
    });
  }
})