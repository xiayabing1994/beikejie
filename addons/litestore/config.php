<?php

return array (
  0 => 
  array (
    'name' => 'LiteName',
    'title' => '小程序名称',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'Fa商城微信小程序',
    'rule' => 'required',
    'msg' => '',
    'tip' => '小程序顶部展示标题',
    'ok' => '',
    'extend' => '',
  ),
  1 => 
  array (
    'name' => 'TopTextColor',
    'title' => '小程序标题颜色',
    'type' => 'radio',
    'content' => 
    array (
      10 => '黑色',
      20 => '白色',
    ),
    'value' => '20',
    'rule' => 'required',
    'msg' => '',
    'tip' => '顶部导航文字颜色',
    'ok' => '',
    'extend' => '',
  ),
  2 => 
  array (
    'name' => 'BackgroundColor',
    'title' => '小程序导航',
    'type' => 'color',
    'content' => 
    array (
    ),
    'value' => '#6d189e',
    'rule' => 'required',
    'msg' => '',
    'tip' => '顶部背景色',
    'ok' => '',
    'extend' => '',
  ),
  3 => 
  array (
    'name' => 'Indexotice',
    'title' => '首页通知栏',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '欢迎使用Fastadmin移动端商城插件，在这里您将得到最优质的体验...',
    'rule' => '',
    'msg' => '',
    'tip' => '首页顶部通知栏文字',
    'ok' => '',
    'extend' => '',
  ),
  4 => 
  array (
    'name' => 'AppID',
    'title' => '小程序ID',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'wXXXXXXXXXX2',
    'rule' => 'required',
    'msg' => '',
    'tip' => '关联小程序AppID',
    'ok' => '',
    'extend' => '',
  ),
  5 => 
  array (
    'name' => 'AppSecret',
    'title' => '小程序密钥',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '879XXXXXXXXXX3a',
    'rule' => 'required',
    'msg' => '',
    'tip' => '关联小程序AppSecret',
    'ok' => '',
    'extend' => '',
  ),
  6 => 
  array (
    'name' => 'MCHID',
    'title' => '小程序商户号',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '1XXXXXXXXXX02',
    'rule' => '',
    'msg' => '',
    'tip' => '小程序的微信支付商户号',
    'ok' => '',
    'extend' => '',
  ),
  7 => 
  array (
    'name' => 'APIKEY',
    'title' => '小程序支付密钥',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'yuwenhaoxiangliantongxinhenjiuleXXXXXXXXXX',
    'rule' => '',
    'msg' => '',
    'tip' => '小程序的微信支付密钥',
    'ok' => '',
    'extend' => '',
  ),
  8 => 
  array (
    'name' => 'MCHIDGZH',
    'title' => '公众号商户号',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => '12XXXXXXXXXX701',
    'rule' => '',
    'msg' => '',
    'tip' => '公众号的微信支付商户号',
    'ok' => '',
    'extend' => '',
  ),
  9 => 
  array (
    'name' => 'APIKEYGZH',
    'title' => '公众号支付密钥',
    'type' => 'string',
    'content' => 
    array (
    ),
    'value' => 'yuXXXXXXXXXXjiule',
    'rule' => '',
    'msg' => '',
    'tip' => '公众号的微信支付密钥',
    'ok' => '',
    'extend' => '',
  ),
  10 => 
  array (
    'name' => 'freight',
    'title' => '运费组合设置',
    'type' => 'radio_text',
    'content' => 
    array (
      10 => '叠加',
      20 => '以最低运费结算',
      30 => '以最高运费结算',
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => 
    array (
      10 => '订单中有多个运费时，取每个商品的运费之和为总运费',
      20 => '订单中有多个运费时，取订单中运费最少的商品的运费为总运费',
      30 => '订单中有多个运费时，取订单中运费最多的商品的运费为总运费',
    ),
    'ok' => '',
    'extend' => '',
  ),
  11 => 
  array (
    'name' => 'ShuoMing',
    'title' => '插件配置说明',
    'type' => 'radio',
    'content' => 
    array (
      10 => '公众号请点击这里查看说明',
    ),
    'value' => '10',
    'rule' => 'required',
    'msg' => '',
    'tip' => '公众号的用户关联的AppSecret和AppSecret，请在第三方登录插件中，配置。',
    'ok' => '',
    'extend' => '',
  ),
);
