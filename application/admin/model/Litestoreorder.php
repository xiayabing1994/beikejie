<?php

namespace app\admin\model;

use think\Model;

class Litestoreorder extends Model
{
    // 表名
    protected $name = 'litestore_order';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = [
        'pay_status_text',
        'pay_time_text',
        'freight_status_text',
        'freight_time_text',
        'receipt_status_text',
        'receipt_time_text',
        'order_status_text'
    ];
    

    
    public function getPayStatusList()
    {
        return ['10' => __('Pay_status 10'),'20' => __('Pay_status 20')];
    }     

    public function getFreightStatusList()
    {
        return ['10' => __('Freight_status 10'),'20' => __('Freight_status 20')];
    }     

    public function getReceiptStatusList()
    {
        return ['10' => __('Receipt_status 10'),'20' => __('Receipt_status 20')];
    }     

    public function getOrderStatusList()
    {
        return ['10' => __('Order_status 10'),'20' => __('Order_status 20'),'30' => __('Order_status 30')];
    }     


    public function getPayStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['pay_status']) ? $data['pay_status'] : '');
        $list = $this->getPayStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getPayTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['pay_time']) ? $data['pay_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getFreightStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['freight_status']) ? $data['freight_status'] : '');
        $list = $this->getFreightStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getFreightTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['freight_time']) ? $data['freight_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getReceiptStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['receipt_status']) ? $data['receipt_status'] : '');
        $list = $this->getReceiptStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getReceiptTimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['receipt_time']) ? $data['receipt_time'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }


    public function getOrderStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['order_status']) ? $data['order_status'] : '');
        $list = $this->getOrderStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    protected function setPayTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setFreightTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    protected function setReceiptTimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }


    public function address()
    {
        return $this->hasOne('Litestoreorderaddress', 'order_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

    public function goods()
    {
        return $this->hasMany('Litestoreordergoods','order_id','id');
    }
    public function user()
    {
        return $this->belongsTo('user');
    }
}
