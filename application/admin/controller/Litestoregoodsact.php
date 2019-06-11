<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use app\admin\model\Litestorespec as SpecModel;
use app\admin\model\Litestorespecvalue as SpecValueModel;
/**
 *
 *
 * @icon fa fa-circle-o
 */
class Litestoregoodsact extends Backend
{
    private $SpecModel;
    private $SpecValueModel;
    /**
     * Litestoregoods模型对象
     * @var \app\admin\model\Litestoregoods
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->SpecModel = new SpecModel;
        $this->SpecValueModel = new SpecValueModel;

        $this->model = new \app\admin\model\Litestoregoods;
        $this->view->assign("specTypeList", $this->model->getSpecTypeList());
        $this->view->assign("deductStockTypeList", $this->model->getDeductStockTypeList());
        $this->view->assign("goodsStatusList", $this->model->getGoodsStatusList());
        $this->view->assign("isDeleteList", $this->model->getIsDeleteList());

        $this->view->assign("spec_attr", '');
        $this->view->assign("spec_list", '');
    }

    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */


    /**
     * 查看
     */
    public function index()
    {
        //当前是否为关联查询
        $this->relationSearch = true;
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax())
        {
            //如果发送的来源是Selectpage，则转发到Selectpage
            if ($this->request->request('keyField'))
            {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['category','freight'])
                ->where($where)
                ->where('goods_type','activity')
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->with(['category','freight'])
                ->where($where)
                ->where('goods_type','activity')
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach ($list as $row) {

                $row->getRelation('category')->visible(['name']);
                $row->getRelation('freight')->visible(['name']);
            }
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function addSpec($spec_name, $spec_value){
        // 判断规格组是否存在
        if (!$specId = $this->SpecModel->getSpecIdByName($spec_name)) {
            // 新增规格组and规则值
            if ($this->SpecModel->add($spec_name)
                && $this->SpecValueModel->add($this->SpecModel['id'], $spec_value))
                return $this->success('', '', [
                    'spec_id' => (int)$this->SpecModel['id'],
                    'spec_value_id' => (int)$this->SpecValueModel['id'],
                ]);
            return $this->error();
        }
        //return ;
        // 判断规格值是否存在
        if ($specValueId = $this->SpecValueModel->getSpecValueIdByName($specId, $spec_value)) {
            return $this->success('', '', [
                'spec_id' => (int)$specId,
                'spec_value_id' => (int)$specValueId,
            ]);
        }
        // 添加规则值
        if ($this->SpecValueModel->add($specId, $spec_value))
            return $this->success('', '', [
                'spec_id' => (int)$specId,
                'spec_value_id' => (int)$this->SpecValueModel['id'],
            ]);
        return $this->error();
    }


    /**
     * 添加规格值
     */
    public function addSpecValue($spec_id, $spec_value)
    {
        // 判断规格值是否存在
        if ($specValueId = $this->SpecValueModel->getSpecValueIdByName($spec_id, $spec_value)) {
            return $this->success('', '', [
                'spec_value_id' => (int)$specValueId,
            ]);
        }
        // 添加规则值
        if ($this->SpecValueModel->add($spec_id, $spec_value))
            return $this->success('', '', [
                'spec_value_id' => (int)$this->SpecValueModel['id'],
            ]);
        return $this->error();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            if ($params) {
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
                    //\think\Log::write('hawk-1 result'.json_encode($result), \think\Log::NOTICE);
                    if ($result !== false) {
                        //成功之后 存储商品规格
                        $spec_many_params = $this->request->post("spec_many/a");
                        //\think\Log::write('hawk0 spec_many_params'.json_encode($spec_many_params), \think\Log::NOTICE);
                        $this->model->addGoodsSpec($params,$spec_many_params,$this->request->post("spec/a"));
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
                // 删除商品sku
                $v->removesku();

                $count += $v->delete();
            }
            if ($count) {
                $this->success();
            } else {
                $this->error(__('No rows were deleted'));
            }
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }

    /**
     * 编辑
     */
    public function edit($ids = NULL)
    {
        $row = $this->model->get($ids, ['specRel', 'spec', 'spec_rel.spec']);
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
                try {
                    //是否采用模型验证
                    if ($this->modelValidate) {
                        $name = basename(str_replace('\\', '/', get_class($this->model)));
                        $validate = is_bool($this->modelValidate) ? ($this->modelSceneValidate ? $name . '.edit' : true) : $this->modelValidate;
                        $row->validate($validate);
                    }
                    $result = $row->allowField(true)->save($params);
                    if ($result !== false) {
                        //成功之后 存储商品规格
                        $spec_many_params = $this->request->post("spec_many/a");
                        $row->addGoodsSpec($params,$spec_many_params,$this->request->post("spec/a"), true);
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
        // 多规格信息
        $specData = 'null';
        if ($row['spec_type'] === '20'){
            $specData = json_encode($this->model->getManySpecData($row['spec_rel'], $row['spec']));
        }
        $row['specData'] = $specData;
        $this->view->assign("row", $row);
        return $this->view->fetch();
    }
}
