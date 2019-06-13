<?php
namespace  app\api\controller;

use app\common\controller\Api;
use app\common\model\User;
use app\admin\model\Usercash;
use think\Db;
use think\Validate;
class Goods extends Api
{
    protected $noNeedRight = '*';

    public function _initialize()
    {
        parent::_initialize();
    }
    public function wholeGoodsList(){
        $goodsModel=new \addons\litestore\model\Wxlitestoregoods();
        return $this->success('个人商品列表',$goodsModel->getWholeList($this->auth->id));
    }
    public function changeGoodsState(){

    }
    public function updGoodsPrice(){
        $specModel=new \addons\litestore\model\Litestoregoodsspec();
        $rq_data = $this->request->request();
        $goods_id = $rq_data['goods_id'];
        $goods_sku_id = $rq_data['goods_sku_id'];
        $price = $rq_data['goods_price'];
        $specModel->where(['goods_id'=>$goods_id,'spec_sku_id'=>$goods_sku_id])->update(['goods_price'=>$price]);
        return $this->success('修改价格成功');
    }
}