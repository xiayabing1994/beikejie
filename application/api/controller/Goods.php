<?php
namespace  app\api\controller;

use app\common\controller\Api;
use app\common\model\User;
use app\admin\model\Usercash;
use think\Db;
use think\Validate;
use app\admin\model\Litestoregoods;
use app\admin\model\Litestoreordergoods;
use addons\litestore\model\Litestorewholesale;
class Goods extends Api
{
    protected $noNeedRight = '*';
    private $goods=null;
    public function _initialize()
    {
        parent::_initialize();
        $this->goods=new \addons\litestore\model\Wxlitestoregoods();
    }
    public function wholeGoodsList(){
        return $this->success('个人商品列表',$this->goods->getWholeList($this->auth->id));
    }

    /**
     * 挂售商品上架
     */
    public function goodsCancel(){
        $goods_id=$this->request->param('goods_id');
        $deal_password=$this->request->param('deal_password');
        $wholesale=new Litestorewholesale();
        $userinfo=$this->auth->getUser();
        if($userinfo['deal_password']!=md5(md5($deal_password))){
            $this->error('交易密码错误,请重新输入');
        }
        if($wholesale->where('goods_id',$goods_id)->update(['goods_status'=>'cancel'])){
            Litestoregoods::where('goods_id',$goods_id)->update(['goods_status'=>20]);
            $this->success('撤销成功');
        }else{
            $this->error('已撤销,请勿重复操作');
        }
    }
    public function goodsFetch(){
        $goods_id=$this->request->param('goods_id');
        $deal_password=$this->request->param('deal_password');
        $wholesale=new Litestorewholesale();
        $userinfo=$this->auth->getUser();
        if($userinfo['deal_password']!=md5(md5($deal_password))){
            $this->error('交易密码错误,请重新输入');
        }
        $wholeinfo=$wholesale->where('goods_id',$goods_id)->find();
        //修改商品订单表状态  删除wholesale表数据
        if(Litestoreordergoods::where('id',$wholeinfo['order_goods_id'])->update(['is_fetch'=>1])){
            $wholesale::where('goods_id',$goods_id)->delete();
            $this->success('提货成功');
        }else{
            $this->error('已提货,请勿重复操作');
        }
    }
    public function updGoodsPrice(){
        $specModel=new \addons\litestore\model\Litestoregoodsspec();
        $rq_data = $this->request->request();
        $goods_id = $rq_data['goods_id'];
        $goods_sku_id = $rq_data['goods_sku_id'];
        $price = $rq_data['goods_price'];
        $deal_password = $rq_data['deal_password'];
        $userinfo=$this->auth->getUser();
        if($userinfo['deal_password']!=md5(md5($deal_password))){
            $this->error('交易密码错误,请重新输入');
        }
        Litestorewholesale::where('goods_id',$goods_id)->update(['goods_status'=>'onsale']);
        $goodsinfo=Litestorewholesale::where('goods_id',$goods_id)->find();
        if($goodsinfo['w_period']=='curr') Litestoregoods::where('goods_id',$goods_id)->update(['goods_status'=>10]);
        $specModel->where(['goods_id'=>$goods_id,'spec_sku_id'=>$goods_sku_id])->update(['goods_price'=>$price]);
        return $this->success('修改价格成功');
    }
}