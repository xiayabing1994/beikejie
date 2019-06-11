<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use fast\Tree;
/**
 * 商品分类
 *
 * @icon fa fa-circle-o
 */
class Litestorecategory extends Backend
{
    
    /**
     * Litestorecategory模型对象
     * @var \app\admin\model\Litestorecategory
     */
    protected $model = null;
    protected $categorylist = [];

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Litestorecategory;

        $tree = Tree::instance();
        $tree->init(collection($this->model->order('weigh desc,id desc')->select())->toArray(), 'pid');
        $this->categorylist = $tree->getTreeList($tree->getTreeArray(0), 'name');
        $categorydata = [0 => ['type' => 'all', 'name' => __('None')]];
        foreach ($this->categorylist as $k => $v)
        {
            $categorydata[$v['id']] = $v;
        }
        $this->view->assign("parentList", $categorydata);
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    public function index()
    {
        if ($this->request->isAjax())
        {
            $search = $this->request->request("search");
            $type = "all";

            //构造父类select列表选项数据
            $list = [];

                foreach ($this->categorylist as $k => $v)
                {
                    if ($search) {
                        if (stripos($v['name'], $search) !== false || stripos($v['nickname'], $search) !== false)
                        {
                            if($type == "all" || $type == null) {
                                $list = $this->categorylist;
                            } else {
                                $list[] = $v;
                            }
                        }
                    } else {
                        if($type == "all" || $type == null) {
                            $list = $this->categorylist;
                        }
                    }

                }

            $total = count($list);
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }

}
