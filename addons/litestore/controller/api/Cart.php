<?php

namespace addons\litestore\controller\api;

use app\common\controller\Api;
use app\common\library\Auth;
use addons\litestore\model\CacheCart;

class Cart extends Api
{
	protected $noNeedLogin = [];
	protected $noNeedRight = ['*'];
    //设置返回的会员字段
    protected $allowFields = ['id', 'username', 'nickname', 'mobile', 'avatar', 'score', 'level', 'bio', 'balance','group_id'];
	public function _initialize()
    {
        parent::_initialize();
        Auth::instance()->setAllowFields($this->allowFields);
        $this->user_id = $this->auth->id;
        $this->model = new CacheCart($this->user_id);
    }

  	/* 加入购物车*/
    public function add()
    {
    	$rq_data = $this->request->request();
    	$goods_id = $rq_data['goods_id'];
    	$goods_num = $rq_data['goods_num'];
    	$goods_sku_id = $rq_data['goods_sku_id'];

        if (!$this->model->add($goods_id, $goods_num, $goods_sku_id)) {
            return $this->error($this->model->getError() ?: '加入购物车失败');
        }
        $total_num = $this->model->getTotalNum();
        return $this->success('加入购物车成功',['cart_total_num' => $total_num]);
    }

    //获得购物车商品数量
    public function getTotalNum(){
        $total_num = $this->model->getTotalNum();
        return $this->success('',['cart_total_num' => $total_num]);
    }

    public function getlists()
    {
        return $this->success('',$this->model->getList($this->user_id));
    }

    public function sub(){
        $rq_data = $this->request->request();
        $goods_id = $rq_data['goods_id'];
        $goods_sku_id = $rq_data['goods_sku_id'];
        $this->model->sub($goods_id, $goods_sku_id);
        return $this->success();
    }
    public function delete()
    {
        $rq_data = $this->request->request();
        $goods_id = $rq_data['goods_id'];
        $goods_sku_id = $rq_data['goods_sku_id'];
        $this->model->delete($goods_id, $goods_sku_id);
        return $this->success();
    }
    public function clear(){
        $this->model->clearAll();
        return $this->success('清空成功');
    }

}

