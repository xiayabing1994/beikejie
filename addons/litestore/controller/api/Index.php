<?php

namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\litestore\model\Wxlitestoregoods;
use addons\litestore\model\Litestorenews;

//http://192.168.123.83/addons/litestore/api.index/index
class Index extends Api
{
	protected $noNeedLogin = ['*'];

	public function _initialize()
    {
        parent::_initialize();
    }

	public function index()
    {
    	$Temp_litestoregoods = new Wxlitestoregoods();
        $banner = new Litestorenews();
        $bannerdata = $banner->where('status', 'normal')->order('updatetime', 'desc')->limit(10)->select();
        $bannerList = [];
        foreach ($bannerdata as $index => $item) {
            $bannerList[] = ['image' => cdnurl($item['image'], true), 'title' => $item['title'],'id'=> $item['id']];
        }

        $NewList = $Temp_litestoregoods->getNewList();
        foreach ($NewList as $index => $item) {
            $NewList[$index]['ImageFrist'] = cdnurl(explode(",",$item['images'])[0], true);
        }

        $Randomlist = $Temp_litestoregoods->getRandom8();
        foreach ($Randomlist as $index => $item) {
            $Randomlist[$index]['ImageFrist'] = cdnurl(explode(",",$item['images'])[0], true);
        }

    	$this->success('', [
    							'NewList' => $NewList,
								'Randomlist' => $Randomlist,
                                'bannerlist' => $bannerList
    					  ]);
    }
    public function appindex()
    {
        $Temp_litestoregoods = new Wxlitestoregoods();
        $banner = new Litestorenews();
        $bannerdata = $banner->where('status', 'normal')->order('updatetime', 'desc')->limit(10)->select();
        $bannerList = [];
        foreach ($bannerdata as $index => $item) {
            $bannerList[] = ['image' => cdnurl($item['image'], true), 'title' => $item['title'],'id'=> $item['id']];
        }

        $HotList = $Temp_litestoregoods->getHot8();
        foreach ($HotList as $index => $item) {
            $HotList[$index]['ImageFrist'] = cdnurl(explode(",",$item['images'])[0], true);
        }
        $this->success('', [
            'HotList' => $HotList,
            'bannerlist' => $bannerList
        ]);
    }
    public function goodslist(){
        $Temp_litestoregoods = new Wxlitestoregoods();
	    $type=$this->request->request('type','activity');
        $typeList = $Temp_litestoregoods->getTypeList($type);
        foreach ($typeList as $index => $item) {
            $typeList[$index]['ImageFrist'] = cdnurl(explode(",",$item['images'])[0], true);
        }
        $this->success('', [
            'list' => $typeList,
        ]);

    }
    public function getnew(){
        $new_id = $this->request->request('new_id');
        $newdata = Litestorenews::get($new_id);
        $newdata['image'] =  cdnurl($newdata['image'], true);
        $newdata['updatetime'] = datetime($newdata['updatetime']);
        $this->success('', [
                                'newdata' => $newdata
                          ]);
    }

}


