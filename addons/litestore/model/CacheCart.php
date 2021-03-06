<?php
namespace addons\litestore\model;

use think\Cache;

/**
 * 购物车管理
 * Class Cart
 */
class CacheCart
{
    /* @var string $error 错误信息 */
    public $error = '';

    /* @var int $user_id 用户id */
    private $user_id;

    /* @var array $cart 购物车列表 */
    private $cart = [];

    /* @var bool $clear 是否清空购物车 */
    private $clear = false;

    /**
     * 构造方法
     * Cart constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
        $this->cart = Cache::get('cart_' . $this->user_id) ?: [];
    }

    /**
     * 购物车列表
     */
    public function getList($user_id)
    {
        // 商品列表
        $goodsList = [];
        $goodsIds = array_unique(array_column($this->cart, 'goods_id'));
        foreach ((new Wxlitestoregoods)->getListByIds($goodsIds) as $goods) {
            $goodsList[$goods['goods_id']] = $goods;
        }
        // 当前用户收货城市id
        $defaultcity = Litestoreadress::getdefault($user_id);
        $cityId = $defaultcity ? $defaultcity['city_id'] : null;

        // 是否存在收货地址
        $exist_address = $defaultcity;
        // 商品是否在配送范围
        $intraRegion = true;
        // 购物车商品列表
        $cartList = [];
        foreach ($this->cart as $key => $cart) {
            // 判断商品不存在则自动删除
            if (!isset($goodsList[$cart['goods_id']])) {
                $this->delete($cart['goods_id'], $cart['goods_sku_id']);
                continue;
            }
            /* @var Goods $goods */
            $goods = $goodsList[$cart['goods_id']];
            // 商品sku信息
            $goods['goods_sku_id'] = $cart['goods_sku_id'];
            // 商品sku不存在则自动删除
            if (!$goods['goods_sku'] = $goods->getGoodsSku($cart['goods_sku_id'])) {
                $this->delete($cart['goods_id'], $cart['goods_sku_id']);
                continue;
            }
            $goods['show_error'] = 0;
            // 判断商品是否下架
            if ($goods['goods_status']!== '10') {
                $goods['show_error'] = 1;
                $goods['show_error_text'] = '已下架';
                $this->setError('很抱歉，商品 [' . $goods['goods_name'] . '] 已下架');
            }
            // 判断商品库存
            if ($cart['goods_num'] > $goods['goods_sku']['stock_num']) {
                $goods['show_error'] = 2;
                $goods['show_error_text'] = '库存不足';
                $this->setError('很抱歉，商品 [' . $goods['goods_name'] . '] 库存不足');
            }
            // 商品单价
            $goods['goods_price'] = $goods['goods_sku']['goods_price'];
            $goods['goods_score'] = $goods['goods_sku']['goods_score'];
            $goods['goods_quota'] = $goods['goods_sku']['goods_quota'];
            if($goods['goods_type']=='wholesale'){
                $user_quota=Litestoreuser::get($user_id)['quota'];
                $discount=Litestorediscount::where('goods_id','=',$cart['goods_id'])
                    ->where('goods_quota','<=',$user_quota)
                    ->order('goods_quota','desc')
                    ->find();
                $discount= !$discount ? 5 : $discount['goods_discount'];   //根据用户配额获取折扣
                $goods['goods_price']=$goods['goods_price']*$discount/10;
            }
            // 商品总价
            $goods['total_num'] = $cart['goods_num'];
            $goods['total_price'] = $total_price = bcmul($goods['goods_price'], $cart['goods_num'], 2);
            $goods['total_score'] = $totalScore = bcmul($goods['goods_score'], $cart['goods_num'], 2);
            $goods['total_quota'] = $totalQuota = bcmul($goods['goods_quota'], $cart['goods_num'], 2);
            // 商品总重量
            $goods['goods_total_weight'] = bcmul($goods['goods_sku']['goods_weight'], $cart['goods_num'], 2);
            // 验证用户收货地址是否存在运费规则中
            if ($intraRegion = $goods['freight']->checkAddress($cityId)) {
                $goods['express_price'] = $goods['freight']->calcTotalFee(
                    $cart['goods_num'], $goods['goods_total_weight'], $cityId);
            } else {
                //$goods['show_error'] = 3;
                //$goods['show_error_text'] = '收货区域无法配送';
                $exist_address && $this->setError("很抱歉，您的收货地址不在商品 [{$goods['goods_name']}] 的配送范围内");
            }
            $cartList[] = $goods->toArray();
        }
        // 商品总金额
        $orderTotalPrice = array_sum(array_column($cartList, 'total_price'));
        $orderTotalScore = array_sum(array_column($cartList, 'total_score'));
        $orderTotalQuota = array_sum(array_column($cartList, 'total_quota'));
        // 所有商品的运费金额
        $allExpressPrice = array_column($cartList, 'express_price');
        // 订单总运费金额
        $expressPrice = $allExpressPrice ? Litestorefreight::freightRule($allExpressPrice) : 0.00;
        return [
            'goods_list' => $cartList,                       // 商品列表
            'order_total_num' => $this->getTotalNum(),       // 商品总数量
            'order_total_price' => round($orderTotalPrice, 2),              // 商品总金额 (不含运费)
            'order_pay_price' => bcadd($orderTotalPrice, $expressPrice, 2),    // 实际支付金额
            'address' => $defaultcity,  // 默认地址
            'exist_address' => $exist_address,      // 是否存在收货地址
            'express_price' => $expressPrice,       // 配送费用
            'intra_region' => $intraRegion,         // 当前用户收货城市是否存在配送规则中
            'has_error' => $this->hasError(),
            'error_msg' => $this->getError(),
            'total_score'=>round($orderTotalScore, 2),
            'total_quota'=>round($orderTotalQuota, 2),
        ];
    }

    /**
     * 添加购物车
     * @param $goods_id
     * @param $goods_num
     * @param $goods_sku_id
     * @return bool
     * @throws \think\exception\DbException
     */
    public function add($goods_id, $goods_num, $goods_sku_id)
    {
        // 购物车商品索引
        $index = $goods_id . '_' . $goods_sku_id;
        // 商品信息
        $goods = Wxlitestoregoods::detail($goods_id);
        // 商品sku信息
        $goods['goods_sku'] = $goods->getGoodsSku($goods_sku_id);
        // 判断商品是否下架
        if ($goods['goods_status'] !== '10') {
            $this->setError('很抱歉，该商品已下架');
            return false;
        }
        // 判断商品库存
        $cartGoodsNum = $goods_num + (isset($this->cart[$index]) ? $this->cart[$index]['goods_num'] : 0);
        if ($cartGoodsNum > $goods['goods_sku']['stock_num']) {
            $this->setError('很抱歉，商品库存不足');
            return false;
        }
        $create_time = time();
        $data = compact('goods_id', 'goods_num', 'goods_sku_id', 'create_time');
        if (empty($this->cart)) {
            $this->cart[$index] = $data;
            return true;
        }
        isset($this->cart[$index]) ? $this->cart[$index]['goods_num'] = $cartGoodsNum : $this->cart[$index] = $data;
        return true;
    }

    /**
     * 减少购物车中某商品数量
     * @param $goods_id
     * @param $goods_sku_id
     */
    public function sub($goods_id, $goods_sku_id)
    {
        $index = $goods_id . '_' . $goods_sku_id;
        $this->cart[$index]['goods_num'] > 1 && $this->cart[$index]['goods_num']--;
    }

    /**
     * 删除购物车中指定商品
     * @param $goods_id
     * @param $goods_sku_id
     */
    public function delete($goods_id, $goods_sku_id)
    {
        $index = $goods_id . '_' . $goods_sku_id;
        unset($this->cart[$index]);
    }

    /**
     * 获取当前用户购物车商品总数量
     * @return int
     */
    public function getTotalNum()
    {
        return array_sum(array_column($this->cart, 'goods_num'));
    }

    /**
     * 析构方法
     * 将cart数据保存到缓存文件
     */
    public function __destruct()
    {
        $this->clear !== true && Cache::set('cart_' . $this->user_id, $this->cart, 86400 * 15);
    }

    /**
     * 清空当前用户购物车
     */
    public function clearAll()
    {
        $this->clear = true;
        Cache::rm('cart_' . $this->user_id);
    }

    /**
     * 设置错误信息
     * @param $error
     */
    private function setError($error)
    {
        empty($this->error) && $this->error = $error;
    }

    /**
     * 是否存在错误
     * @return bool
     */
    private function hasError()
    {
        return !empty($this->error);
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

}
