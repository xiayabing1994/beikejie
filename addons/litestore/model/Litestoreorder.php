<?php

namespace addons\litestore\model;

use think\Model;
use think\Db;

class Litestoreorder extends Model
{
    // 表名
    protected $name = 'litestore_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    // 追加属性
    protected $append = [
        'pay_status_text',
        'pay_time_text',
        'freight_status_text',
        'freight_time_text',
        'receipt_status_text',
        'receipt_time_text',
        'order_status_text',
        'creattime_text',
    ];

    public function cancel($user_id,$order_id){
        if ($this['pay_status'] === '20') {
            $this->error = '已付款订单不可取消';
            return false;
        }
        $goods = $this['goods'];
        $this->backGoodsStock($goods);

        $order = self::get([
            'id' => $order_id,
            'user_id' => $user_id,
            'order_status' => ['<>', '20']
        ]);

        return $order->save([
            'order_status' => '20'
        ]);
    }
    private function backGoodsStock(&$goodsList)
    {
        $goodsSpecSave = [];
        foreach ($goodsList as $goods) {
            // 下单减库存
            if ($goods['deduct_stock_type'] === '10') {
                $goodsSpecSave[] = [
                    'goods_spec_id' => $goods['goods_spec_id'],
                    'stock_num' => ['inc', $goods['total_num']]
                ];
            }
        }
        if(!empty($goodsSpecSave)){
        // 更新商品规格库存
            return (new Litestoregoodsspec)->isUpdate()->saveAll($goodsSpecSave);
        }
        return true;
    }

    public function finish($user_id,$order_id){
        if (!$order = self::get([
            'id' => $order_id,
            'user_id' => $user_id,
            'order_status' => ['<>', '20']
        ])) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        if ($order['freight_status'] === '10' || $order['receipt_status'] === '20') {
            $order->error = '该订单不合法';
            return false;
        }
        return $order->save([
            'receipt_status' => '20',
            'receipt_time' => time(),
            'order_status' => '30'
        ]);
    }

    public function checkGoodsStatusFromOrder($goodsList){
        foreach ($goodsList as $goods) {
            //商品是否下架
            if ($goods['goods']['goods_status'] !== '10') {
                $this->setError('很抱歉，商品 [' . $goods['goods_name'] . '] 已下架');
                return false;
            }
            //付款减库存
            if ($goods['deduct_stock_type'] === '20' && $goods['spec']['stock_num'] < 1) {
                $this->setError('很抱歉，商品 [' . $goods['goods_name'] . '] 库存不足');
                return false;
            }
        }
        return true;
    }

    public function getList($user_id){
        return $this->with(['goods'])
            ->where('user_id', '=', $user_id)
            ->where('order_status', '<>', '20')
            ->order(['createtime' => 'desc'])->limit(50)
            ->select();
    }

    public function getOrderDetail($order_id, $user_id){
        if (!$order = self::get([
            'order_id' => $order_id,
            'litestoreorder.user_id' => $user_id,
            'order_status' => ['<>', '20']
        ], ['goods'=>['spec','goods'], 'address'])) {
            throw new BaseException(['msg' => '订单不存在']);
        }
        $goodsList = [];
        foreach ($order['goods'] as $index => $item) {
            $item['image'] = cdnurl(explode(",",$item['images'])[0], true);
            $item['sku_image'] = $item['spec']['spec_image']=='' ? '' : cdnurl($item['spec']['spec_image'], true);
            $goodsList[] = $item;
        }
        $order['goods'] = $goodsList;
        return $order;
    }

    public function payDetail($order_no){
        return self::get(['order_no' => $order_no, 'pay_status' => 10], ['goods']);
    }

    public function getCart($user_id){
        $model = new CacheCart($user_id);
        return $model->getList($user_id);
    }
    
    public function CarclearAll($user_id){
        $Card = new CacheCart($user_id);
        $Card->clearAll();
    }

    public function getBuyNow($user_id, $goods_id, $goods_num, $goods_sku_id,$goods_type='activity')
    {
        // 商品信息
        $goods = Wxlitestoregoods::detail($goods_id);
        $goods['show_error'] = 0;
        // 判断商品是否下架
        if ($goods['goods_status'] !== '10') {
            $goods['show_error'] = 1;
            $goods['show_error_text'] = '已下架';
            $this->setError('很抱歉，商品信息不存在或已下架');
        }
        // 商品sku信息
        $goods['goods_sku'] = $goods->getGoodsSku($goods_sku_id);
        // 判断商品库存
        if ($goods_num > $goods['goods_sku']['stock_num']) {
            $goods['show_error'] = 2;
            $goods['show_error_text'] = '库存不足';
            $this->setError('很抱歉，商品库存不足');
        }
        // 商品单价
        $goods['goods_price'] = $goods['goods_sku']['goods_price'];
        $goods['goods_score'] = $goods['goods_sku']['goods_score'];
        $goods['goods_quota'] = $goods['goods_sku']['goods_quota'];
        if($goods['goods_type']=='wholesale'){
            $user_quota=Litestoreuser::get($user_id)['quota'];
            $discount=Litestorediscount::where('goods_id','=',$goods_id)
                ->where('goods_quota','<=',$user_quota)
                ->order('goods_quota','desc')
                ->find();
            $discount= !$discount ? 5 : $discount['goods_discount'];   //根据用户配额获取折扣
            $goods['goods_price']=$goods['goods_price']*$discount/10;
        }

        // 商品总价
        $goods['total_num'] = $goods_num;
        $goods['total_price'] = $totalPrice = bcmul($goods['goods_price'], $goods_num, 2);
        $goods['total_score'] = $totalScore = bcmul($goods['goods_score'], $goods_num, 2);
        $goods['total_quota'] = $totalQuota = bcmul($goods['goods_quota'], $goods_num, 2);
        // 商品总重量
        $goods_total_weight = bcmul($goods['goods_sku']['goods_weight'], $goods_num, 2);
        // 当前用户收货城市id
        $defaultcity = Litestoreadress::getdefault($user_id);
        $cityId = $defaultcity ? $defaultcity['city_id'] : null;
        // 是否存在收货地址
        $exist_address = $defaultcity;
        // 验证用户收货地址是否存在运费规则中
        if (!$intraRegion = $goods['freight']->checkAddress($cityId)) {
            $exist_address && $this->setError('很抱歉，您的收货地址不在配送范围内');
        }

        // 计算配送费用
        $expressPrice = $intraRegion ?
            $goods['freight']->calcTotalFee($goods_num, $goods_total_weight, $cityId) : 0;
        return [
            'goods_list' => [$goods],               // 商品详情
            'order_total_num' => $goods_num,        // 商品总数量
            'order_total_price' => $totalPrice,    // 商品总金额 (不含运费)
            'order_pay_price' => bcadd($totalPrice, $expressPrice, 2),  // 实际支付金额
            'address' => $defaultcity,  // 默认地址
            'exist_address' => $exist_address,  // 是否存在收货地址
            'express_price' => $expressPrice,    // 配送费用
            'intra_region' => $intraRegion,    // 当前用户收货城市是否存在配送规则中
            'has_error' => $this->hasError(),
            'error_msg' => $this->getError(),
            'total_score'=>$totalScore,
            'total_quota'=>$totalQuota,
        ];
    }
    public function getBuyNowWhole($user_id, $goods_id, $goods_num, $goods_sku_id)
    {
        // 商品信息
        $goods = Wxlitestoregoods::detail($goods_id);
        $goods['show_error'] = 0;
        // 判断商品是否下架
        if ($goods['goods_status'] !== '10') {
            $goods['show_error'] = 1;
            $goods['show_error_text'] = '已下架';
            $this->setError('很抱歉，商品信息不存在或已下架');
        }
        // 商品sku信息
        $goods['goods_sku'] = $goods->getGoodsSku($goods_sku_id);
        // 判断商品库存
        if ($goods_num > $goods['goods_sku']['stock_num']) {
            $goods['show_error'] = 2;
            $goods['show_error_text'] = '库存不足';
            $this->setError('很抱歉，商品库存不足');
        }
        // 商品单价
        $goods['goods_price'] = $goods['goods_sku']['goods_price'];
        $goods['goods_score'] = $goods['goods_sku']['goods_score'];
        $goods['goods_quota'] = $goods['goods_sku']['goods_quota'];
        $user_quota=Litestoreuser::get($user_id)['quota'];
        $discount=Litestorediscount::where('goods_id','=',$goods_id)
            ->where('goods_quota','<=',$user_quota)
            ->order('goods_quota','desc')
            ->find();
        $discount= !$discount ? 5 : $discount['goods_discount'];   //根据用户配额获取折扣
        $goods['goods_price']=$goods['goods_price']*$discount/10;
        // 商品总价
        $goods['total_num'] = $goods_num;
        $goods['total_price'] = $totalPrice = bcmul($goods['goods_price'], $goods_num, 2);
        $goods['total_score'] = $totalScore = bcmul($goods['goods_score'], $goods_num, 2);
        $goods['total_quota'] = $totalQuota = bcmul($goods['goods_quota'], $goods_num, 2);
        // 商品总重量
        $goods_total_weight = bcmul($goods['goods_sku']['goods_weight'], $goods_num, 2);
        // 当前用户收货城市id
        $defaultcity = Litestoreadress::getdefault($user_id);
        $cityId = $defaultcity ? $defaultcity['city_id'] : null;
        // 是否存在收货地址
        $exist_address = $defaultcity;
        // 验证用户收货地址是否存在运费规则中
        if (!$intraRegion = $goods['freight']->checkAddress($cityId)) {
            $exist_address && $this->setError('很抱歉，您的收货地址不在配送范围内');
        }

        // 计算配送费用
        $expressPrice = $intraRegion ?
            $goods['freight']->calcTotalFee($goods_num, $goods_total_weight, $cityId) : 0;
        return [
            'goods_list' => [$goods],               // 商品详情
            'order_total_num' => $goods_num,        // 商品总数量
            'order_total_price' => $totalPrice,    // 商品总金额 (不含运费)
            'order_pay_price' => bcadd($totalPrice, $expressPrice, 2),  // 实际支付金额
            'address' => $defaultcity,  // 默认地址
            'exist_address' => $exist_address,  // 是否存在收货地址
            'express_price' => $expressPrice,    // 配送费用
            'intra_region' => $intraRegion,    // 当前用户收货城市是否存在配送规则中
            'has_error' => $this->hasError(),
            'error_msg' => $this->getError(),
            'total_score'=>$totalScore,
            'total_quota'=>$totalQuota,
            'goods_type'=>'wholesale'
        ];
    }
    public function order_add($user_id, $order){
        if (empty($order['address'])) {
            $this->error = '请先选择收货地址';
            return false;
        }
        $userinfo=\app\common\model\User::get($user_id);
        if($userinfo['score']<$order['total_score']){
            $this->error='积分不足';
            return false;
        }elseif($userinfo['score']<$order['total_quota']){
             $this->error='配额不足';
            return false;
        }
        Db::startTrans();
        // 记录订单信息
        $this->save([
            'user_id' => $user_id,
            'order_no' => $this->orderNo(),
            'total_price' => $order['order_total_price'],
            'pay_price' => $order['order_pay_price'],
            'express_price' => $order['express_price'],
        ]);
        // 订单商品列表
        $goodsList = [];
        // 更新商品库存 (下单减库存)
        $deductStockData = [];
        foreach ($order['goods_list'] as $goods) {
        if($goods['goods_type']=='wholesale' && $userinfo['identy']=='customer'){
            $this->error='您还不是消费商,请到活动专区消费';
            return false;
        }
            /* @var Goods $goods */
            $goodsList[] = [
                'user_id' => $user_id,
                'goods_id' => $goods['goods_id'],
                'goods_name' => $goods['goods_name'],
                'images' => $goods['images'],
                'deduct_stock_type' => $goods['deduct_stock_type'],
                'spec_type' => $goods['spec_type'],
                'spec_sku_id' => $goods['goods_sku']['spec_sku_id'],
                'goods_spec_id' => $goods['goods_sku']['goods_spec_id'],
                'goods_attr' => $goods['goods_sku']['goods_attr'],
                'content' => $goods['content'],
                'goods_no' => $goods['goods_sku']['goods_no'],
                'goods_price' => $goods['goods_sku']['goods_price'],
                'goods_score' => $goods['goods_sku']['goods_score'],
                'goods_quota' => $goods['goods_sku']['goods_quota'],
                'line_price' => $goods['goods_sku']['line_price'],
                'goods_weight' => $goods['goods_sku']['goods_weight'],
                'total_num' => $goods['total_num'],
                'total_price' => $goods['total_price'],
                'total_score' => $goods['total_score'],
                'total_quota' => $goods['total_quota'],
                'goods_type'  =>$goods['goods_type'],
            ];
            // 下单减库存
            $goods['deduct_stock_type'] === '10' && $deductStockData[] = [
                'goods_spec_id' => $goods['goods_sku']['goods_spec_id'],
                'stock_num' => ['dec', $goods['total_num']]
            ];
        }
        // 保存订单商品信息
        $this->goods()->saveAll($goodsList);
        // 更新商品库存
        !empty($deductStockData) && (new Litestoregoodsspec)->isUpdate()->saveAll($deductStockData);
        // 记录收货地址
        $this->address()->save([
            'user_id' => $user_id,
            'name' => $order['address']['name'],
            'phone' => $order['address']['phone'],
            'province_id' => $order['address']['province_id'],
            'city_id' => $order['address']['city_id'],
            'region_id' => $order['address']['region_id'],
            'detail' => $order['address']['detail'],
        ]);
        Db::commit();
        return true;
    }
    
    public function getPayStatusList()
    {
        return ['10' => __('Pay_status 10'),'20' => __('Pay_status 20')];
    }     

    public function getFreightStatusList()
    {
        return ['10' => __('Freight_status 10'),'20' => __('Freight_status 20')];
    }     

    public function getReceiptStatusList()
    {
        return ['10' => __('Receipt_status 10'),'20' => __('Receipt_status 20')];
    }     

    public function getOrderStatusList()
    {
        return ['10' => __('Order_status 10'),'20' => __('Order_status 20'),'30' => __('Order_status 30')];
    }     


    public function getPayStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['pay_status']) ? $data['pay_status'] : '');
        $list = $this->getPayStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_time']) ? $data['pay_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getFreightStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['freight_status']) ? $data['freight_status'] : '');
        $list = $this->getFreightStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFreightTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['freight_time']) ? $data['freight_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getReceiptStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['receipt_status']) ? $data['receipt_status'] : '');
        $list = $this->getReceiptStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getReceiptTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['receipt_time']) ? $data['receipt_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getOrderStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['order_status']) ? $data['order_status'] : '');
        $list = $this->getOrderStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function getCreattimeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['createtime']) ? $data['createtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;;
    }

    protected function setPayTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setFreightTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setReceiptTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function goods()
    {
        return $this->hasMany('Litestoreordergoods','order_id', 'id');
    }

    public function address()
    {
        return $this->hasOne('Litestoreorderaddress', 'order_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    protected function orderNo()
    {
        return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    public function hasError()
    {
        return !empty($this->error);
    }

    public function updatePayStatus($transaction_id)
    {
        Db::startTrans();
        // 更新商品库存、销量
        $GoodsModel = new Wxlitestoregoods;
        $GoodsModel->updateStockSales($this['goods']);
        // 更新订单状态
        $this->save([
            'pay_status' => '20',
            'pay_time' => time(),
            'transaction_id' => $transaction_id,
        ]);
        Db::commit();
        return true;
    }
}
