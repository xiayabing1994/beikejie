<?php

namespace addons\litestore\model;

use think\Model;

class Litestoreordergoods extends Model
{
    // 表名
    protected $name = 'litestore_order_goods';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [];

    public function goods()
    {
        return $this->belongsTo('Wxlitestoregoods','goods_id','goods_id');
    }

    public function spec()
    {
        return $this->belongsTo('Litestoregoodsspec','spec_sku_id','spec_sku_id');
    }
    public function getImagesAttr($value){
        return cdnurl(explode(',',$value)[0],true);
    }
    
}
