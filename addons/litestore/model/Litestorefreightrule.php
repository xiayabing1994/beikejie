<?php

namespace addons\litestore\model;

use think\Model;

class Litestorefreightrule extends Model
{
    // 表名
    protected $name = 'litestore_freight_rule';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = false;

    protected $append = ['region_data'];
    

    public function getRegionDataAttr($value, $data)
    {
        return explode(',', $data['region']);
    }
}
