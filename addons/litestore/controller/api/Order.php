<?php
namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\litestore\model\Litestoreorder;

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
        $userinfo=$this->auth->getUser();
        $goods_id = $this->request->request('goods_id');
        $goods_num = $this->request->request('goods_num');
        $goods_type = $this->request->request('goods_type');
        $goods_sku_id = $this->request->request('goods_sku_id');

        $order = $this->model->getBuyNow($this->user_id, $goods_id, $goods_num, $goods_sku_id,$goods_type);


        if ($this->model->hasError()) {
            return $this->error($this->model->getError());
        }
        if($goods_type=='score' && $userinfo['score']<$order['total_score']){
            return $this->error('积分不足');
        }elseif($goods_type=='wholesale' && $userinfo['identy']=='customer'){
            return $this->error('您还不是消费商,请到活动专区消费');
        }elseif($goods_type=='rights' && $userinfo['score']<$order['total_quota']){
            return $this->error('配额不足');
        }
         // 创建订单
        if ($this->model->order_add($this->user_id, $order)) {
            // 发起微信支付
            if($this->request->request('type')&&$this->request->request('type')=='gzh'){
                $this->init_wx_pay_for_gzh(true);
                $this->make_wx_pay('wechat');
            }else{
                $this->init_wx_pay_for_wxapp();
                $this->make_wx_pay('wxapp');
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

    public function callback_for_wxgzh(){
        $this->init_wx_pay_for_gzh(false);
        $response = $this->wxapp->payment->handleNotify(function($notify, $successful){
            $order = $this->model->payDetail($notify->out_trade_no);

            if (empty($order)) { // 如果订单不存在
                return true;
                //return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            if ($successful) {
                $order->updatePayStatus($notify->transaction_id);
            }

            return true; // 返回处理完成
        });

        $response->send();
    }

    public function callback_for_wxapp(){
        $this->init_wx_pay_for_wxapp();
        $response = $this->wxapp->payment->handleNotify(function($notify, $successful){
            $order = $this->model->payDetail($notify->out_trade_no);

            if (empty($order)) { // 如果订单不存在
                return true;
                //return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            if ($successful) {
                $order->updatePayStatus($notify->transaction_id);
            }

            return true; // 返回处理完成
        });

        $response->send();
    }
    public function callback(){
            $out_trade_no=$this->request->param('no');
            $transaction_id=$this->request->param('tid');
            $order = $this->model->payDetail($out_trade_no);
            dump($order->toArray());die;
            if (empty($order)) { // 如果订单不存在
//                return true;
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            $goods_type=$order->goods_type;
            if($goods_type=='activity') $this->deal_activity($order);
            //$order->updatePayStatus($transaction_id);
            return '处理成功'; // 返回处理完成
    }
    public function deal_activity($order){
        $usermodel=new \app\common\model\User();
        Db::startTrans();
        try{
            $usermodel->score($order['total_price']*0.05,$order['user_id'],'购物返现积分');
            $usermodel->quota($order['total_price']*0.2,$order['user_id'],'购物返现配额');
            $usermodel->where('id',$this->auth->id)->update(['identy'=>'business']);
            Db::commit();
            return 'success';
        }catch (\Exception $e) {
            Db::rollback();
            return 'fail';
        }

    }

}
