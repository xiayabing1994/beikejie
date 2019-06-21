<?php

namespace addons\litestore\model;

use think\Model;
use app\admin\model\Litestorewholesale as Whole;

class Litestorewholesale extends Whole
{
    protected $append=['goods_status_text'];
    public function getGoodsStatusTextAttr($value){
        return $value=='fetch' ? '待提货' : '已挂售';
    }
}