<?php

namespace app\admin\model;
use think\Model;
/**
 * 规格/属性(组)模型
 * Class Spec
 * @package app\store\model
 */
class Litestorespec extends Model
{

    // 表名
    protected $name = 'litestore_spec';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = '';

    /**
     * 根据规格组名称查询规格id
     * @param $spec_name
     * @return mixed
     */
    public function getSpecIdByName($spec_name)
    {
        return self::where(compact('spec_name'))->value('id');
    }

    /**
     * 新增规格组
     * @param $spec_name
     * @return false|int
     */
    public function add($spec_name)
    {
        return $this->save(compact('spec_name'));
    }

}
