<?php

namespace addons\litestore\model;
use think\Model;

class Litestoregoodsspec extends Model
{

    // 表名
    protected $name = 'litestore_goods_spec';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $append=['discount_info'];
    public function getSkuList($goods_id){
        return $this->where('goods_id','=',$goods_id)->select();
    }
    /**
     * 批量添加商品sku记录
     * @param $goods_id
     * @param $spec_list
     * @return array|false
     * @throws \Exception
     */
    public function addSkuList($goods_id, $spec_list)
    {
        $data = [];
        foreach ($spec_list as $item) {
            $data[] = array_merge($item['form'], [
                'spec_sku_id' => $item['spec_sku_id'],
                'goods_id' => $goods_id,
            ]);
        }
        return $this->saveAll($data);
    }

    /**
     * 添加商品规格关系记录
     * @param $goods_id
     * @param $spec_attr
     * @return array|false
     * @throws \Exception
     */
    public function addGoodsSpecRel($goods_id, $spec_attr)
    {
        $data = [];
        array_map(function ($val) use (&$data, $goods_id) {
            array_map(function ($item) use (&$val, &$data, $goods_id) {
                $data[] = [
                    'goods_id' => $goods_id,
                    'spec_id' => $val['group_id'],
                    'spec_value_id' => $item['item_id'],
                ];
            }, $val['spec_items']);
        }, $spec_attr);
        $model = new Litestoregoodsspecrel;
        return $model->saveAll($data);
    }

    /**
     * 移除指定商品的所有sku
     * @param $goods_id
     * @return int
     */
    public function removeAll($goods_id)
    {
        $model = new Litestoregoodsspecrel;
        $model->where('goods_id','=', $goods_id)->delete();
        return $this->where('goods_id','=', $goods_id)->delete();
    }
    public function getGoodsDiscountAttr($value){
        $res=[];
        if(!empty($value)){
            $discount=json_decode($value);
            foreach($discount[0] as $k=>$v){
                if($discount[1][$k]!=0 && $v!=0) $res[$discount[1][$k]]=$v;
            }
        }
        return $res;
    }

    public function getDiscountInfoAttr($value,$data){
        if(!empty($data['goods_discount'])) {
            $discount = json_decode($data['goods_discount']);
            $quota=array_filter($discount[0]);
            $discounts=array_filter($discount[1]);
            rsort($quota);
            sort($discounts);
            $res=[];
            foreach($quota as $k=>$q){
                $res[$k]['discount']=$discounts[$k];
                $res[$k]['quota']=$q;
                $res[$k]['price']=round($discounts[$k]/10*$data['goods_price'],2);
            }
            return $res;
        }
    }

}
