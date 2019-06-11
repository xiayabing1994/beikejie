<?php

namespace addons\litestore\controller\api;
use app\common\controller\Api;
use app\common\library\Auth;
use addons\litestore\model\Litestoreadress;

class Adress extends Api
{
	protected $noNeedLogin = [];
	protected $noNeedRight = ['*'];

	public function _initialize()
    {
        parent::_initialize();
        $this->user_id = $this->auth->id;
        $this->model = new Litestoreadress;
    }

	public function lists()
    {
        $list =  $this->model->getList($this->user_id);
        return $this->success('',['list' => $list]);
    }

    public function add()
    {
        if ($this->model->add($this->user_id, $this->request->post())) {
            return $this->success('添加成功');
        }
        return $this->error('添加失败');
    }

    public function setdefault(){
    	 if ($this->model->setdefault($this->user_id,$this->request->request('id'))){
    		return $this->success('设置成功');
    	 }
    	  return $this->error('设置失败');
    }
    public function del()
    {
        if ($this->model->del($this->request->request('id'))) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }

    public function detail(){
        $id = $this->request->request('id');
        $detail = $this->model->detail($this->user_id,$id);
        $rArea = array_values($detail['Area']);
        return $this->success('成功',compact('detail', 'rArea'));
    }

    public function edit(){
        $id = $this->request->request('id');
        $detail = $this->model->detail($this->user_id,$id);
        if ($detail->edit($this->request->post())) {
            return $this->success('成功');
        }
        return $this->error('更新失败');

    }
}