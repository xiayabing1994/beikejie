<?php

namespace app\admin\model;

use think\Model;

class Litestoregoods extends Model
{
    // 表名
    protected $name = 'litestore_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'spec_type_text',
        'deduct_stock_type_text',
        'goods_status_text',
        'is_delete_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['goods_sort' => $row[$pk]]);
        });
    }

    
    public function getSpecTypeList()
    {
        return ['10' => __('Spec_type 10'),'20' => __('Spec_type 20')];
    }     

    public function getDeductStockTypeList()
    {
        return ['10' => __('Deduct_stock_type 10'),'20' => __('Deduct_stock_type 20')];
    }     

    public function getGoodsStatusList()
    {
        return ['10' => __('Goods_status 10'),'20' => __('Goods_status 20')];
    }     

    public function getIsDeleteList()
    {
        return ['0' => __('Is_delete 0'),'1' => __('Is_delete 1')];
    }     


    public function getSpecTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['spec_type']) ? $data['spec_type'] : '');
        $list = $this->getSpecTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getDeductStockTypeTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['deduct_stock_type']) ? $data['deduct_stock_type'] : '');
        $list = $this->getDeductStockTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getGoodsStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['goods_status']) ? $data['goods_status'] : '');
        $list = $this->getGoodsStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getIsDeleteTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['is_delete']) ? $data['is_delete'] : '');
        $list = $this->getIsDeleteList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function category()
    {
        return $this->belongsTo('litestorecategory', 'category_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function freight()
    {
        return $this->belongsTo('Litestorefreight', 'delivery_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    /**
     * 关联商品规格表
     */
    public function spec()
    {
        return $this->hasMany('Litestoregoodsspec','goods_id','goods_id');
    }

    /**
     * 关联商品规格关系表
     */
    public function specRel()
    {
        return $this->belongsToMany('Litestorespecvalue', 'litestore_goods_spec_rel','spec_value_id','goods_id');
    }

    /**
     * 计算显示销量 (初始销量 + 实际销量)
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getGoodsSalesAttr($value, $data)
    {
        return $data['sales_initial'] + $data['sales_actual'];
    }

    /**
     * 添加商品规格
     * @param $data
     * @param $isUpdate
     * @throws \Exception
     */
    public function addGoodsSpec(&$data,$params,$specparams,$isUpdate = false)
    {
        // 更新模式: 先删除所有规格
        $model = new Litestoregoodsspec;
        $isUpdate && $model->removeAll($this['goods_id']);
        // 添加规格数据
        if ($data['spec_type'] === '10') {
            // 单规格
            $this->spec()->save($specparams);
        } else if ($data['spec_type'] === '20') {
            // 添加商品与规格关系记录
            $model->addGoodsSpecRel($this['goods_id'],$params['spec_attr']);
            // 添加商品sku
            $model->addSkuList($this['goods_id'],$params['spec_list']);
        }
    }

    public function removesku(){
        // 删除商品sku
        (new Litestoregoodsspec)->removeAll($this['goods_id']);
    }
    /**
     * 获取规格信息
     */
    public function getManySpecData($spec_rel, $skuData)
    {
        // spec_attr
        $specAttrData = [];
        foreach ($spec_rel as $item) {
            if (!isset($specAttrData[$item['spec_id']])) {
                $specAttrData[$item['spec_id']] = [
                    'group_id' => $item['spec']['id'],
                    'group_name' => $item['spec']['spec_name'],
                    'spec_items' => [],
                ];
            }
            $specAttrData[$item['spec_id']]['spec_items'][] = [
                'item_id' => $item['pivot']['spec_value_id'],
                'spec_value' => $item['spec_value'],
            ];
        }

        // spec_list
        $specListData = [];
        foreach ($skuData as $item) {
            $specListData[] = [
                'goods_spec_id' => $item['goods_spec_id'],
                'spec_sku_id' => $item['spec_sku_id'],
                'rows' => [],
                'form' => [
                    'goods_no' => $item['goods_no'],
                    'goods_price' => $item['goods_price'],
                    'goods_weight' => $item['goods_weight'],
                    'line_price' => $item['line_price'],
                    'stock_num' => $item['stock_num'],
                    'spec_image' => $item['spec_image'],
                ],
            ];
        }
        return ['spec_attr' => array_values($specAttrData), 'spec_list' => $specListData];
    }

}
