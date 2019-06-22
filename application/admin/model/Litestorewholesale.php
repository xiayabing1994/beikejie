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
    public  function getPeriodInfo(){
        $currinfo=self::where('w_period','curr')->where('start','>',0)->find();
        $nextinfo=self::where('w_period','next')->where('start','>',0)->find();
        $totalinfo=$this->group('w_period')->field('count(*) as count,w_period')->select();
        foreach($totalinfo as $total){
            $totalinfo[$total['w_period']]=$total['count'];
        }
        return ['curr'=>$currinfo,'next'=>$nextinfo,'total'=>$totalinfo];
    }
    public function getStartAttr($value){
        return date('Y-m-d H:i:s',$value);
    }
    public function getEndAttr($value){
        return date('Y-m-d H:i:s',$value);
    }
}
