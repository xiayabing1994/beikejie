<?php

namespace app\admin\model;

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

    protected $append = ['region_content'];

    static $regionAll;
    static $regionTree;
    
    public function getRegionContentAttr($value, $data)
    {
        // 当前区域记录转换为数组
        $regionIds = explode(',', $data['region']);

        if (count($regionIds) === 373) return '全国';

        // 所有地区
        if (empty(self::$regionAll)) {
            self::$regionAll = Litestorearea::getCacheAll();
            self::$regionTree = Litestorearea::getCacheTree();
        }
        // 将当前可配送区域格式化为树状结构
        $alreadyTree = [];
        foreach ($regionIds as $regionId)
            $alreadyTree[self::$regionAll[$regionId]['pid']][] = $regionId;
        $str = '';
        foreach ($alreadyTree as $provinceId => $citys) {
            $str .= self::$regionTree[$provinceId]['name'];
            if (count($citys) !== count(self::$regionTree[$provinceId]['city'])) {
                $cityStr = '';
                foreach ($citys as $cityId)
                    $cityStr .= self::$regionTree[$provinceId]['city'][$cityId]['name'];
                $str .= ' (<span class="am-link-muted">' . mb_substr($cityStr, 0, -1, 'utf-8') . '</span>)';
            }
            $str .= '、';
        }
        return mb_substr($str, 0, -1, 'utf-8');
    }
}
