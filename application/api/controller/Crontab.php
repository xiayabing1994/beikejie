<?php
namespace app\api\controller;
use think\Db;
use \addons\litestore\model\Wxlitestoregoods;
use \app\admin\model\Litestorewholesale;
use \app\admin\model\Litestoreordergoods;
use \app\common\model\User;
class Crontab{

    private $_goods=null;
    private $_whole=null;
    public function __construct(){
        $this->_goods=new Wxlitestoregoods();
        $this->_whole=new Litestorewholesale();
    }
    public function endCurrentPeroid(){
        Db::startTrans();
        $goods_arr=$this->_whole
            ->where('w_period','curr')
            ->field('goods_id,user_id,order_id')
            ->select();
        foreach($goods_arr as $goods){
            //下架商品
            $this->_goods->where('goods_id',$goods['goods_id'])->update(['goods_status'=>20]);
            $goodsinfo=$this->_goods->detail($goods['goods_id'])->toArray();
            foreach($goodsinfo['spec'] as $specitem){
                //返还余额与配额
                if($specitem['stock_num']>0) {
                    $where=['order_id'=>$goods['order_id'],'goods_id'=>$goods['goods_id'],'goods_spec_id'=>$specitem['goods_spec_id']];
                    $ordergoods=Litestoreordergoods::where($where)->find();
                    User::money($specitem['stock_num']*$ordergoods['goods_price'],$goods['user_id'],'未售出商品返还余额');
                    User::quota($specitem['stock_num']*$ordergoods['goods_quota'],$goods['user_id'],'未售出商品返还配额');
                }
            }
        }
        //本期结束
//        $this->_whole->where('w_period','curr')->update(['w_period'=>'pass']);
        //下期开始
//        $this->_whole->where(['w_period'=>'next','goods_status'=>'onsale'])->update(['w_period'=>'curr','start'=>time(),'end'=>time()+86400*5]);
        //上架商品
        $queryFunc=function($query) {
            $query->name('wholesale')
                ->where(['w_period' => 'current', 'goods_status' => 'onsale'])
                ->field('goods_id')
                ->select();
        };
        $this->goods->where('goods_id','IN',$queryFunc)->update(['goods_tsatus'=>10]);
        Db::commit();

        //1.更改wholesale所有当前期记录peroid为pass
        //2.更改当前期中所有商品状态为下架状态
        //3.查询当前期中所有商品,计算出未卖出商品数量,返还用户配额和余额
        //4.更改下期商品状态为当期，所有已挂售商品状态为上架，删除未挂售商品，更改所有记录开始时间与结束时间
    }
}