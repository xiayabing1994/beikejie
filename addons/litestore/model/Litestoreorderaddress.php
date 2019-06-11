<?php

namespace addons\litestore\model;

use think\Model;
use addons\litestore\model\Area as AddArea;

class Litestoreorderaddress extends Model
{
    // 表名
    protected $name = 'litestore_order_address';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = ['Area'];
    
    public function getAreaAttr($value, $data)
    {
        return [
            'province' => AddArea::getNameById($data['province_id']),
            'city' => AddArea::getNameById($data['city_id']),
            'region' => AddArea::getNameById($data['region_id']),
        ];
    }

}
