<?php

namespace addons\litestore\model;

use think\Model;
use addons\litestore\Litestore as litestore_add;

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
        'method_text','freightprice'
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
    public function freightprice()
    {
        return $this->hasMany('Litestorefreightrule')->field('first,first_fee,additional,additional_fee,region');
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

    /**
     * 运费模板详情
     * @param $delivery_id
     * @return null|static
     * @throws \think\exception\DbException
     */
    public function detail($delivery_id)
    {
        return self::get($delivery_id, ['rule']);
    }

    public function calcTotalFee($total_num, $total_weight, $city_id)
    {
        $rule = [];  // 当前规则
        foreach ($this['rule'] as $item) {
            if (in_array($city_id, $item['region_data'])) {
                $rule = $item;
                break;
            }
        }
        // 商品总数量or总重量
        $total = $this['method']=== '10' ? $total_num : $total_weight;
        if ($total <= $rule['first']) {
            return number_format($rule['first_fee'], 2);
        }
        // 续件or续重 数量
        $additional = $total - $rule['first'];
        if ($additional <= $rule['additional']) {
            return number_format($rule['first_fee'] + $rule['additional_fee'], 2);
        }
        // 计算续重/件金额
        if ($rule['additional'] < 1) {
            // 配送规则中续件为0
            $additionalFee = 0.00;
        } else {
            $additionalFee = bcdiv($rule['additional_fee'], $rule['additional'], 2) * $additional;
        }
        return number_format($rule['first_fee'] + $additionalFee, 2);
    }

    public static function freightRule($allExpressPrice)
    {
        $Temp_litestore = new litestore_add();
        $wxapp = $Temp_litestore->GetCfg();

        $freight_rule = $wxapp['freight'];
        $expressPrice = 0.00;
        switch ($freight_rule) {
            case '10':    // 叠加
                $expressPrice = array_sum($allExpressPrice);
                break;
            case '20':    // 以最低运费结算
                $expressPrice = min($allExpressPrice);
                break;
            case '30':    // 以最高运费结算
                $expressPrice = max($allExpressPrice);
                break;
        }
        return $expressPrice;
    }

    public function checkAddress($city_id)
    {
        $cityIds = explode(',', implode(',', array_column($this['rule'], 'region')));
        return in_array($city_id, $cityIds);
    }
}
