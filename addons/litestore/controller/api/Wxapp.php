<?php

namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\litestore\litestore as litestore_add;

//http://192.168.123.83/addons/litestore/api.wxapp/base
class Wxapp extends Api
{
	protected $noNeedLogin = ['*'];

	public function _initialize()
    {
        parent::_initialize();
    }

	public function base()
    {
        $Temp_litestore = new litestore_add();
        $wxapp = $Temp_litestore->GetCfg();

        //格式化导航文字颜色
        $wxapp['TopTextColor'] = $wxapp['TopTextColor']=='10'? '#000000':'#ffffff' ;

        $wxapp['APIKEY'] = $wxapp['AppID'] = $wxapp['AppSecret'] = $wxapp['MCHIDGZH']= $wxapp['APIKEYGZH']= $wxapp['MCHID']= $wxapp['APIKEY'] = '';
        $this->success('', ['wxapp' => $wxapp]);
    }

}