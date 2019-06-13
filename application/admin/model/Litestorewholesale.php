<?php

namespace app\admin\model;
use think\Model;
/**
 * 规格/属性(组)模型
 * Class Spec
 * @package app\store\model
 */
class Litestorewholesale extends Model
{

    // 表名
    protected $name = 'litestore_wholesale';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';


}
