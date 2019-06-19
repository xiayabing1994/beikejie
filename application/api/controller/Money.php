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
        $param['user_id']=$this->auth->id;
        $cashModel->allowField(true)->save($param);
        Db::commit();
        $this->success('提现成功');
    }
    public function propertyLog(){
        $property=$this->request->param('property','money');
        $page=$this->request->param('page',1);
        $limit=$this->request->param('limit',8);
        $table_name="user_".$property."_log";
        $data=Db::name($table_name)
            ->where('user_id',$this->auth->id)
            ->order('createtime','desc')
            ->page($page,$limit)
            ->select();
        foreach($data as $k=>$row) $data[$k]['createtime']=date('Y-m-d H:i:s',$row['createtime']);
        $this->success($property.':资产变动记录',$data);
    }
    /**
     * 提现记录 需要登录
     * @param $status 提现状态: wait=待审核,checked=已审核,refuse=未通过,remited=已处理
     * @param int $page
     */
    public function cashlog()
    {
        $status=$this->request->param('status','wait');
        $page=$this->request->param('page',1);
        $limit=$this->request->param('limit',8);
        $user = $this->auth->getUserinfo();
        $statusList = ['wait', 'refuse', 'checked', 'remited'];
        if(!in_array($status, $statusList))
            $this->error('参数错误');
        $list = Usercash::where(['user_id' => $user['id'], 'status' => $status])
            ->page($page, $limit)
            ->order('id desc')
            ->select();

        $this->success('提现记录', $list);
    }
}