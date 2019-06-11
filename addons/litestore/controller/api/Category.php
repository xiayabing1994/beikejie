<?php

namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\litestore\model\Litestorecategory;
use fast\Tree;

//http://192.168.123.83/addons/litestore/api.category/list
class Category extends Api
{
	protected $noNeedLogin = ['*'];

	public function _initialize()
    {
        parent::_initialize();
    }

    public function Showlist(){
 		$tree = Tree::instance();
        $tree->init(collection(litestorecategory::order('weigh desc,id desc')->select())->toArray(), 'pid');
        $categorydata = $tree->getTreeArray(0);
        foreach ($categorydata as $index => $item) {
            foreach ($item['childlist'] as $indexImg => $itemImg) {
                $categorydata[$index]['childlist'][$indexImg]['ImageFrist'] = cdnurl($itemImg['image'], true);
            }
        }
        $this->success('', [
    							'categorydata'  => $categorydata
    					  ]);
    }

}