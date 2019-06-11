<?php

namespace addons\litestore\model;

use think\Model;

class Litestoreadress extends Model
{

	// 表名
    protected $name = 'litestore_adress';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 追加属性
    protected $append = ['Area'];



    public function getList($user_id)
    {
        return self::all(compact('user_id'));
    }

	public function getAreaAttr($value, $data)
    {
        return [
            'province' => Area::getNameById($data['province_id']),
            'city' => Area::getNameById($data['city_id']),
            'region' => Area::getNameById($data['region_id']),
        ];
    }

    public function add($use_id, $data)
    {

        $listdata = $this->all(['user_id'=>$use_id]);
        foreach ($listdata as $key => $value) {
        	$value['isdefault'] = '0';
        	$value->save();
        }

        // 添加收货地址
        $region = explode(',', $data['region']);
        $province_id = Area::getIdByName($region[0], 1);
        $city_id = Area::getIdByName($region[1], 2, $province_id);
        $region_id = Area::getIdByName($region[2], 3, $city_id);
        $this->allowField(true)->save(array_merge([
            'user_id' => $use_id,
            'province_id' => $province_id,
            'city_id' => $city_id,
            'region_id' => $region_id,
            'isdefault' => '1'
        ], $data));

        return true;
    }

    public function setdefault($use_id,$id){
    	$listdata = $this->all(['user_id'=>$use_id]);
        foreach ($listdata as $key => $value) {
        	$value['isdefault'] = '0';
        	$value->save();
        }
    	return ($this->get($id))->save(['isdefault' => '1']);
    }

   	public function del($id){
        return ($this->get($id))->delete();
    }

    public function detail($user_id, $address_id)
    {
        return self::get(compact('user_id', 'address_id'));
    }

    public function edit($data){
        $region = explode(',', $data['region']);
        $province_id = Area::getIdByName($region[0], 1);
        $city_id = Area::getIdByName($region[1], 2, $province_id);
        $region_id = Area::getIdByName($region[2], 3, $city_id);

        return $this->allowField(true)
            ->save(array_merge(compact('province_id', 'city_id', 'region_id'), $data));
    }

    public static function  getdefault($use_id){
        $filter = [];
        $filter['isdefault'] = '1';
        $filter['user_id'] = $use_id;
       return self::get($filter);
    }
}
