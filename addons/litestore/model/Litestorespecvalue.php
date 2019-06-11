<?php

namespace addons\litestore\model;
use think\Model;
/**
 * 规格/属性(组)模型
 * Class Spec
 * @package app\store\model
 */
class Litestorespecvalue extends Model
{

    // 表名
    protected $name = 'litestore_spec_value';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';

    /**
     * 根据规格组名称查询规格id
     * @param $spec_id
     * @param $spec_value
     * @return mixed
     */
    public function getSpecValueIdByName($spec_id, $spec_value)
    {
        return self::where(compact('spec_id', 'spec_value'))->value('id');
    }

    /**
     * 新增规格值
     * @param $spec_id
     * @param $spec_value
     * @return false|int
     */
    public function add($spec_id, $spec_value)
    {
        return $this->save(compact('spec_value', 'spec_id'));
    }
    
    /**
     * 关联规格组表
     * @return $this|\think\model\relation\BelongsTo
     */
    public function spec()
    {
        return $this->belongsTo('Litestorespec','spec_id','id');
    }

}
