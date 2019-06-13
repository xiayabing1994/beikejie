<?php
namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\litestore\model\Litestoreorder;
use addons\litestore\model\Wxlitestoregoods;
use addons\litestore\model\Litestoreordergoods;

use addons\third\model\Third;
use EasyWeChat\Foundation\Application as WXPAY_APP;
use EasyWeChat\Payment\Order as WXPAY_ORDER;
use think\Db;
class Order extends Api
{
	protected $noNeedRight = ['*'];
    protected $noNeedLogin = ['callback_for_wxapp','callback_for_wxgzh'];

	public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->auth->id;
        $this->model = new Litestoreorder;

    }

    public function buyNow()
    {
        $goods_id = $this->request->request('goods_id');
        $goods_num = $this->request->request('goods_num');
        $goods_sku_id = $this->request->request('goods_sku_id');

        $order = $this->model->getBuyNow($this->user_id, $goods_id, $goods_num, $goods_sku_id);

        return $this->success('获取成功',$order);
    }

    public function buyNow_pay(){
        $goods_id = $this->request->request('goods_id');
        $goods_num = $this->request->request('goods_num');
        $goods_sku_id = $this->request->request('goods_sku_id');
            $order = $this->model->getBuyNow($this->user_id, $goods_id, $goods_num, $goods_sku_id);
        if ($this->model->hasError()) {
            return $this->error($this->model->getError());
        }

         // 创建订单
        if ($this->model->order_add($this->user_id, $order)) {
            // 发起微信支付
            if($paytype=$this->request->request('pay_type')){
                $this->app_pay($paytype);
            }
        }
        $error = $this->model->getError() ?: '订单创建失败';
        return $this->error($error);

    }

    public function Get_order_num(){
        $NoPayNum = $this->model->where(['user_id' => $this->user_id, 'pay_status' => '10', 'freight_status' => '10', 'order_status' => '10', 'receipt_status' => '10'])->count();
        $NoFreightNum = $this->model->where(['user_id' => $this->user_id, 'pay_status' => '20', 'freight_status' => '10', 'order_status' => '10', 'receipt_status' => '10'])->count();
        $NoReceiptNum = $this->model->where(['user_id' => $this->user_id, 'pay_status' => '20', 'freight_status' => '20', 'order_status' => '10', 'receipt_status' => '10'])->count();
        return $this->success('',['NoPayNum'=>$NoPayNum,'NoFreightNum'=>$NoFreightNum,'NoReceiptNum'=>$NoReceiptNum]);
    }

    public function my(){
        $list = $this->model->getList($this->user_id);
        return $this->success('',$list);
    }

    public function detail(){
        $id = $this->request->request('id');
        $order = $this->model->getOrderDetail($id, $this->user_id);
        return $this->success('',['order' => $order]);
    }

	public function cart_pay()
    {
        $order = $this->model->getCart($this->user_id);
  
        // 创建订单
        if ($this->model->order_add($this->user_id, $order)) {
            // 清空购物车
            $this->model->CarclearAll($this->user_id);
           
            // 发起微信支付
            if($this->request->request('type')&&$this->request->request('type')=='gzh'){
                $this->init_wx_pay_for_gzh(true);
                $this->make_wx_pay('wechat');
            }else{
                $this->init_wx_pay_for_wxapp();
                $this->make_wx_pay('wxapp');
            }
        }
        $this->error($this->model->getError() ?: '订单创建失败');
    }

    public function finish(){
        $id = $this->request->post("id");
        $order = $this->model;
        if ($order->finish($this->user_id,$id)) {
            return $this->success('');
        }
        return $this->error($order->getError());
    }

    public function cancel(){
        $id = $this->request->post("id");
        $order = $this->model->getOrderDetail($id, $this->user_id);

        if ($order->cancel($this->user_id,$id)) {
            return $this->success('');
        }
        return $this->error($order->getError());
    }

    public function order_pay(){
         $id = $this->request->post("id");
         $order = $this->model->getOrderDetail($id, $this->user_id);
         if (!$order->checkGoodsStatusFromOrder($order['goods'])) {
            return $this->error($order->getError());
         }
         $this->model = $order;
         // 发起微信支付
         if($this->request->post('type')&&$this->request->post('type')=='gzh'){
             $this->init_wx_pay_for_gzh(true);
             $this->make_wx_pay('wechat');
         }else{
             $this->init_wx_pay_for_wxapp();
             $this->make_wx_pay('wxapp');
         }
    }

    private function init_wx_pay_for_gzh($Ischeck=false){
        //这里首先判断 此用户是否绑定了微信公众号
        if($Ischeck){
            $third = Third::where(['user_id' => $this->user_id, 'platform' => 'wechat'])->find();
            if(!$third){
                //从这里自动绑定微信公众号的账户
                $this->error('您未绑定微信号',null,1008);
            }
        }

        $config = get_addon_config('litestore');

        $third_config = get_addon_config('third');
        $third_options = array_intersect_key($third_config, array_flip(['wechat']));
        $third_options = $third_options['wechat'];

        $options = [
                    'debug'  => true,
                     'log' => [
                        'level' => 'debug',
                        'file'  => '/tmp/easywechat.log',
                    ],
                    'app_id'   => $third_options['app_id'],
                    'secret'   => $third_options['app_secret'],
                    'payment' => [
                        'merchant_id'        =>  $config['MCHIDGZH'],
                        'key'                =>  $config['APIKEYGZH'],
                        'notify_url'         =>  \think\Request::instance()->domain().addon_url('litestore/api.order/callback_for_wxgzh'),
                    ],

                  ];
        $this->wxapp = new WXPAY_APP($options);
    }

    private function init_wx_pay_for_wxapp(){
        $config = get_addon_config('litestore');
        $options = [
                    'app_id'   => $config['AppID'],
                    'secret'   => $config['AppSecret'],
                    'payment' => [
                        'merchant_id'        =>  $config['MCHID'],
                        'key'                =>  $config['APIKEY'],
                        'notify_url'         =>  \think\Request::instance()->domain().addon_url('litestore/api.order/callback_for_wxapp'),
                    ],

                  ];
        $this->wxapp = new WXPAY_APP($options);
    }

    private function make_wx_pay($platform){
    	$third = Third::where(['user_id' => $this->user_id, 'platform' => $platform])->find();

        $payment = $this->wxapp->payment;

        $attributes = [
		    'trade_type'       => 'JSAPI',
		    'body'             => $this->model['order_no'],
		    'detail'           => 'OrderID:'.$this->model['id'],
		    'out_trade_no'     => $this->model['order_no'],
		    //'total_fee'        => $this->model['pay_price'] * 100, // 单位：分
            'total_fee'        => 1, // 单位：分
		    'openid'           => $third['openid'],
		];
		$order = new WXPAY_ORDER($attributes);

		$result = $payment->prepare($order);
		if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
		    $prepayId = $result->prepay_id;
            $config = $payment->configForJSSDKPayment($prepayId); // 返回数组
			return $this->success('预支付成功',$config);
		}
		return $this->error('微信支付调用失败',$result);
    }
    private function app_pay($paytype){
        $pay = \Yansongda\Pay\Pay::$paytype(\addons\epay\library\Service::getConfig($paytype));
        if($paytype=='alipay'){
                $order = [
                    'out_trade_no' => $this->model['order_no'],//你的订单号
//                    'total_amount' => $this->model['pay_price'],//单位元
                    'total_amount' => 0.01,//单位元
                    'subject'      =>'OrderID:'.$this->model['id'],
                ];

        }elseif($paytype=='wechat'){
            $order = [
                'out_trade_no' => $this->model['order_no'],//你的订单号
                //'total_fee'        => $this->model['pay_price'] * 100, // 单位：分
                'total_fee' =>1,//单位分
                'body'      => $this->model['order_no'],
                'detail'           => 'OrderID:'.$this->model['id'],
            ];
        }
         return $this->success('微信预支付成功',$pay->app($order)->getContent());
    }
  public function appcallback(){
      $type = $this->request->param('type');
      $pay = \addons\epay\library\Service::checkNotify($type);
      if (!$pay) {
          echo '签名错误';
          return;
      }

        //你可以直接通过$pay->verify();获取到相关信息
        //支付宝可以获取到out_trade_no,total_amount等信息
        //微信可以获取到out_trade_no,total_fee等信息
      $data = $pay->verify();
      $out_trade_no=$data['out_trade_no'];
      $transaction_id=$type=='alipay' ? $data['trade_no'] : $data['transaction_id'];
      $order = $this->model->payDetail($out_trade_no);
      if (empty($order)) { // 如果订单不存在
//                return true;
          return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
      }
      foreach($order['goods'] as $ordergoods){
          $goods_type=$ordergoods['goods_type'];
          if($goods_type=='activity') $this->deal_activity($ordergoods);
          if($goods_type=='wholesale') $this->deal_wholesale($ordergoods);
          if($goods_type=='rights')     $this->deal_rights($ordergoods);
      }
      $order->updatePayStatus($transaction_id);
      return '处理成功'; // 返回处理完成
    //下面这句必须要执行,且在此之前不能有任何输出
      echo $pay->success();

      return;
  }


    public function callback(){
            $out_trade_no=$this->request->param('no');
            $transaction_id=$this->request->param('tid');
            $order = $this->model->payDetail($out_trade_no);

            if (empty($order)) { // 如果订单不存在
//                return true;
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            foreach($order['goods'] as $ordergoods){
                $goods_type=$ordergoods['goods_type'];
                if($goods_type=='activity') $this->deal_activity($ordergoods);
                if($goods_type=='wholesale') $this->deal_wholesale($ordergoods);
                if($goods_type=='rights')     $this->deal_rights($ordergoods);
            }
            $order->updatePayStatus($transaction_id);
            return '处理成功'; // 返回处理完成
    }

    public function deal_activity($order){
        $usermodel=new \app\common\model\User();
        $shopconfig=load_config('shop');
        Db::startTrans();
        try{
            $usermodel->score($order['total_price']*$shopconfig['goods_score_rate']/100,$order['user_id'],'购物返现积分');
            $usermodel->quota($order['total_price']*$shopconfig['goods_score_quota']/100,$order['user_id'],'购物返现配额');
            $usermodel->where('id',$this->auth->id)->update(['identy'=>'business']);
            $provider=Wxlitestoregoods::detail($order['goods_id'])['provider'];
            if($provider>0){       //如果为批发商商品,需要返还用户余额与积分,修改litestore_order_goods表中num
                $usermodel->score($order['total_price']*$shopconfig['goodssale_score_rate']/100,$provider,'商品卖出返现积分');
                $usermodel->money($order['total_price']*$shopconfig['goodssale_money_rate']/100,$provider,'商品卖出返还余额');
            }
            Db::commit();
            return 'success';
        }catch (\Exception $e) {
            Db::rollback();
            return 'fail';
        }

    }
    public function deal_wholesale($goodsitem){
        //goods表添加数据         删除id,goods_type,设置provider=order下userid
        //goods_spec表添加数据    删除goods_spec_id,goods_sales  设置stock_num=order下total_num,goods_id为新记录id
        //goods_spec_rel添加数据  删除id,替换goods_id为新纪录id
        //wholesale表添加记录
        //$specModel=new addons\litestore\model\Litestoregoodsspec();
        Db::startTrans();
        try{
                $oldgoods=Wxlitestoregoods::get($goodsitem['goods_id']);
                $newgoods= array_diff_key($oldgoods->toArray(), ["goods_id"=>1, "goods_type"=>1,'goods_sales'=>1]);
                $newgoods['provider']=$goodsitem['user_id'];
                $newgoods['goods_status']=20;      //未上架
                $new=Wxlitestoregoods::create($newgoods);
                $newgoodsid=$new->goods_id;                  //goods表操作结束
                $specList=db('litestore_goods_spec')->where('goods_id',$goodsitem['goods_id'])->select();
                foreach($specList as $spec){
                    if($goodsitem['goods_spec_id']!=$spec['goods_spec_id']) continue;       //如果购买的spec_id与当前不同则不添加
                    $newspec=array_diff_key($spec,['goods_spec_id'=>1,'goods_sales'=>1,'goods_id'=>1]);
                    $newspec['stock_num']=$goodsitem['total_num'];
                    $newspec['goods_id']=$newgoodsid;
                    db('litestore_goods_spec')->insert($newspec);
                }                                            //goods_spec操作结束
                $specrelList=db('litestore_goods_spec_rel')
                    ->where('goods_id',$goodsitem['goods_id'])
                    ->field('spec_id,spec_value_id')
                    ->select();
                foreach($specrelList as $specrel){
                    $specrel['create_time']=time();
                    $specrel['goods_id']=$newgoodsid;
                    db('litestore_goods_spec_rel')->insert($specrel);
                }                                           //goods_spec_rel操作结束
                db('litestore_wholesale')->insert([
                    'goods_id'=>$newgoodsid,
                    'user_id'=>$goodsitem['user_id'],
                    'order_id'=>$goodsitem['order_id'],
                ]);                                         //wholesale批发表添加记录成功
            Db::commit();
            echo  'success';
        }catch (\Exception $e) {
            dump($e->getMessage());
            Db::rollback();
            echo 'fail';
        }

    }
    public function deal_rights($order){
	    //扣除用户quota即可
        $usermodel=new \app\common\model\User();
        Db::startTrans();
        try{
                $usermodel->quota(0-$order['total_quota'],$order['user_id'],'权益专区商品消费扣除');
            Db::commit();

        }catch(\Exception $e){
            Db::rollback();
        }
    }

}
