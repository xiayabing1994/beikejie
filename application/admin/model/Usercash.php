<?php

namespace app\admin\model;

use think\Model;


class Usercash extends Model
{

    

    //数据库
    protected $connection = 'database';
    // 表名
    protected $name = 'usercash';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
        'status_text'
    ];
    protected $type=[
        'createtime'=>'timestamp:Y-m-d H:i:s',
    ];
    

    
    public function getStatusList()
    {
        return ['wait' => __('Status wait'), 'checked' => __('Status checked'), 'refuse' => __('Status refuse'), 'remited' => __('Status remited')];
    }


    public function getStatusTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
