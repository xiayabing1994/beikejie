<?php

namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\litestore\model\Wxlitestoregoods;
use addons\litestore\model\Litestoregoodsspec;

//http://192.168.123.83/addons/litestore/api.goods/detail
class Goods extends Api
{
	protected $noNeedLogin = ['*'];

	public function _initialize()
    {
        parent::_initialize();
    }

	public function detail()
    {
    	$goods_id = $this->request->request('goods_id');
		// 商品详情
        $detail = Wxlitestoregoods::detail($goods_id);
        $imgs=[];
        foreach (explode(",",$detail['images']) as $index => $item) {
            $imgs[] = cdnurl($item, true);
        }
        $detail['imgs_url'] = $imgs;

		if (!$detail || $detail['goods_status'] !== '10') {
			$this->error('很抱歉，商品信息不存在或已下架');
        }
        // 规格信息
        $specData = $detail['spec_type'] === '20' ? $detail->getManySpecData($detail['spec_rel'], $detail['spec']) : null;

		// 这里对规格的img格式化
        if($specData!=null){
            foreach($specData['spec_list'] as $index => $item){
                $specData['spec_list'][$index]["form"]['imgshow'] = $specData['spec_list'][$index]["form"]['spec_image']==='' ? null:cdnurl($specData['spec_list'][$index]["form"]['spec_image'], true);
            }
        }

        $this->success('', [
    							'detail'  => $detail,
                                'specData' => $specData
    					  ]);
    }

    public function category_list(){
        $categoryid = $this->request->request('id');
        $page = $this->request->request('page');
        $sortType = $this->request->request('types');
        $rename = $this->request->request('name');

        // 筛选条件
        $filter = [];
        if($categoryid==-1){
            $filter['goods_name'] = ['like', '%' . trim($rename) . '%'];
        }else{
            $filter['category_id'] = $categoryid;
        }
        $filter['goods_status'] = '10';
        $filter['is_delete'] = '0';

         // 排序规则
        $sort = [];
        if ($sortType === 'normal') {
            $sort = ['goods_sort'=> 'desc', 'goods_id' => 'desc'];
        } elseif ($sortType === 'sales') {
            $sort = ['goods_sales' => 'desc'];
        } elseif ($sortType === 'price') {
            $sort = ['goods_min_price' => 'asc'];
        }

        // 商品表名称
        $model_temp = new Wxlitestoregoods();
        $tableName = $model_temp->getTable();
        // 多规格商品 最高价与最低价
        $GoodsSpec = new Litestoregoodsspec();
        $minPriceSql = $GoodsSpec->field(['MIN(goods_price)'])
            ->where('goods_id', 'EXP', "= `$tableName`.`goods_id`")->buildSql();
        $maxPriceSql = $GoodsSpec->field(['MAX(goods_price)'])
            ->where('goods_id', 'EXP', "= `$tableName`.`goods_id`")->buildSql();
        
        $listdata = $model_temp->field(['*', '(sales_initial + sales_actual) as goods_sales',
            "$minPriceSql AS goods_min_price",
            "$maxPriceSql AS goods_max_price"
        ])->with(['spec'])->where($filter)->order($sort)
        ->paginate(5, false, [ 'page' => $page ]);

        $blistdatarList = [];
        foreach ($listdata as $index => $item) {
            $blistdatarList[] = ['image' => cdnurl(explode(",",$item['images'])[0], true), 'goods_name' => $item['goods_name'],
                                    'id'=> $item['goods_id'],'goods_min_price'=>$item['goods_min_price'],'goods_max_price'=>$item['goods_max_price'],'goods_sales'=>$item['goods_sales']];
        }

        $pagedata = $listdata;
        $this->success('', [
                                'listdata'  => $blistdatarList,
                                'pagedata' => $pagedata,
                          ]);

        
    }
}