<?php

namespace addons\litestore\model;
use think\Model;

class Litestoregoodsspecrel extends Model
{
	// 表名
    protected $name = 'litestore_goods_spec_rel';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = '';

    /**
     * 关联规格组
     * @return \think\model\relation\BelongsTo
     */
    public function spec()
    {
        return $this->belongsTo('Litestorespec');
    }
}