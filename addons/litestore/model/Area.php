<?php
namespace addons\litestore\model;

use think\Cache;
use think\Model;

class Area extends Model
{
	    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = false;
    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;

 	/**
     * 根据id获取地区名称
     * @param $id
     * @return string
     */
    public static function getNameById($id)
    {
        $area = self::getCacheAll();
        return $area[$id]['name'];
    }

    /**
     * 根据名称获取地区id
     * @param $name
     * @param int $level
     * @param int $pid
     * @return mixed
     */
    public static function getIdByName($name, $level = 0, $pid = 0)
    {
        return static::useGlobalScope(false)->where(compact('name', 'level', 'pid'))
            ->value('id') ?: static::add($name, $level, $pid);
    }

    /**
     * @param $name
     * @param int $level
     * @param int $pid
     * @return mixed
     */
    private static function add($name, $level = 0, $pid = 0)
    {
        $model = new static;
        $model->save(compact('name', 'level', 'pid'));
        Cache::rm('area');
        return $model->getLastInsID();
    }

    /**
     * 获取所有地区(树状结构)
     * @return mixed
     */
    public static function getCacheTree()
    {
        return self::areaCache()['tree'];
    }

    /**
     * 获取所有地区
     * @return mixed
     */
    public static function getCacheAll()
    {
        return self::areaCache()['all'];
    }

    /**
     * 获取地区缓存
     * @return mixed
     */
    private static function areaCache()
    {
        if (!Cache::get('area')) {
            // 所有地区
            $all = $allData = self::useGlobalScope(false)->column('id, pid, name, level', 'id');
            // 格式化
            $tree = [];
            foreach ($allData as $pKey => $province) {
                if ($province['level'] === 1) {    // 省份
                    $tree[$province['id']] = $province;
                    unset($allData[$pKey]);
                    foreach ($allData as $cKey => $city) {
                        if ($city['level'] === 2 && $city['pid'] === $province['id']) {    // 城市
                            $tree[$province['id']]['city'][$city['id']] = $city;
                            unset($allData[$cKey]);
                            foreach ($allData as $rKey => $area) {
                                if ($area['level'] === 3 && $area['pid'] === $city['id']) {    // 地区
                                    $tree[$province['id']]['city'][$city['id']]['area'][$area['id']] = $area;
                                    unset($allData[$rKey]);
                                }
                            }
                        }
                    }
                }
            }
            Cache::set('area', compact('all', 'tree'));
        }
        return Cache::get('area');
    }



}
