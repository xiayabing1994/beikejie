<?php

namespace app\admin\model;

use think\Model;
use addons\litestore\litestore as litestore_add;

class Litestorefreight extends Model
{
    // 表名
    protected $name = 'litestore_freight';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'method_text'
    ];
    

    protected static function init()
    {
        self::afterInsert(function ($row) {
            $pk = $row->getPk();
            $row->getQuery()->where($pk, $row[$pk])->update(['weigh' => $row[$pk]]);
        });
    }

    
    public function getMethodList()
    {
        return ['10' => __('Method 10'),'20' => __('Method 20')];
    }     


    public function getMethodTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['method']) ? $data['method'] : '');
        $list = $this->getMethodList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    public function rule()
    {
        return $this->hasMany('Litestorefreightrule');
    }

    public function createDeliveryRule($data)
    {
        $save = [];
        $connt = count($data['region']);
        for ($i = 0; $i < $connt; $i++) {
            $save[] = [
                'region' => $data['region'][$i],
                'first' => $data['first'][$i],
                'first_fee' => $data['first_fee'][$i],
                'additional' => $data['additional'][$i],
                'additional_fee' => $data['additional_fee'][$i],
            ];
        }
        $this->rule()->delete();
        return $this->rule()->saveAll($save);
    }

    public function detail($delivery_id)
    {
        return self::get($delivery_id, ['rule']);
    }

}
