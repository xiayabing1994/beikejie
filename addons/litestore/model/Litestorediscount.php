<?php

namespace addons\litestore\model;

use think\Model;

class Litestorediscount extends Model
{
    // 表名
    protected $name = 'litestore_discount';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

}