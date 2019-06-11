<?php

namespace app\admin\controller;

use app\common\controller\Backend;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Litestorefreight extends Backend
{
    
    /**
     * Litestore_freight模型对象
     * @var \app\admin\model\Litestorefreight
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Litestorefreight;
        $this->view->assign("methodList", $this->model->getMethodList());

        $AreaData = new \app\admin\model\Litestorearea;
        $this->view->assign("regionData", json_encode($AreaData->getCacheTree()));
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
        /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");

            if ($params) {
                $ruleparams = $this->request->post("delivery/a");
                //这里判断是否存在 地区信息
                if(!isset($ruleparams['rule']) || empty($ruleparams['rule'])){
                    $this->error('请选择可配送区域');
                }
                $ruleparams = $ruleparams['rule'];

                if ($this->dataLimit && $this->dataLimitFieldAutoFill) {
                    $params[$this->dataLimitField] = $this->auth->id;
                }
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = str_replace("\\model\\", "\\validate\\", get_class($this->model));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.add' : true) : $this->modelValidate;
                        $this->model->validate($validate);
                    }
                    $result = $this->model->allowField(true)->save($params);
                    if ($result !== false) {
                        //当插入完成后 插入规则
                        $this->model->createDeliveryRule($ruleparams);

                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->detail($ids);
        if (!$row)
            $this->error(__('No Results were found'));
        $adminIds = $this->getDataLimitAdminIds();
        if (is_array($adminIds)) {
            if (!in_array($row[$this->dataLimitField], $adminIds)) {
                $this->error(__('You have no permission'));
            }
        }
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
                $ruleparams = $this->request->post("delivery/a");
                //这里判断是否存在 地区信息
                if(!isset($ruleparams['rule']) || empty($ruleparams['rule'])){
                    $this->error('请选择可配送区域');
                }
                $ruleparams = $ruleparams['rule'];

                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        //当更新完成后 插入规则
                        $row->createDeliveryRule($ruleparams);
                        $this->success();
                    } else {
                        $this->error($row->getError());
                    }
                } catch (\think\exception\PDOException $e) {
                    $this->error($e->getMessage());
                } catch (\think\Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
    /**
     * 删除
     */
    public function del($ids = "")
    {
        if ($ids) {
            $pk = $this->model->getPk();
            $adminIds = $this->getDataLimitAdminIds();
            if (is_array($adminIds)) {
                $count = $this->model->where($this->dataLimitField, 'in', $adminIds);
            }
            $list = $this->model->where($pk, 'in', $ids)->select();
            $count = 0;
            foreach ($list as $k => $v) {
                $count += $v->delete();
                $v->rule()->delete();
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

}
