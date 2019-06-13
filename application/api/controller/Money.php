<?php
namespace  app\api\controller;

use app\common\controller\Api;
use app\common\model\User;
use app\admin\model\Usercash;
use think\Db;
use think\Validate;
class Money extends Api{
    protected $noNeedRight = '*';
    public function _initialize()
    {
        parent::_initialize();
    }
    public function moneycash(){
        $param=$this->request->param();
        $rule=[
            'money'=>'require|float',
            'cash_account'=>'require',
            'cash_realname'=>'require',
            'mobile'=>'require|length:11',
//            'memo'=>'require',
        ];
        $msg=[
            'money.require'=>'金额不能为空',
            'money.float'=>'请输入正确金额',
            'cash_account.require'=>'提现账户不能为空',
            'cash_realname.require'=>'真实姓名不能为空',
            'mobile.length'=>'请输入正确联系方式',
//            'memo.require'=>'请输入提现说明'
        ];
        $validate=new validate($rule,$msg);
        if(!$validate->check($param)){
            $this->error($validate->getError());
        }
        if($this->auth->getUser()['money']<$param['money']){
            $this->error('可用余额不足');
        }
        Db::startTrans();
        User::money(0-$param['money'],$this->auth->id,'提现扣除余额');
        $cashModel=new Usercash();
        unset($param['token']);
        $cashModel->save($param);
        Db::commit();
        $this->success('提现成功');
    }
}