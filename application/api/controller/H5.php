<?php
/**
 * Created by PhpStorm.
 * User: 15636
 * Date: 2019/6/5
 * Time: 22:32
 */

namespace app\api\controller;


use app\common\service\TkPid;
use app\common\service\TkToken;
use think\Controller;

class H5 extends Controller
{
    public function tao_notify()
    {
        $state = $this->request->get('state');
        $code = $this->request->get('code');

        if(empty($code) || empty($state)) {
            $this->error('非法访问', '');
        }

        $user = \app\api\model\User::get($state);

        if(empty($user['taobao_pid'])) {
            $result = TkToken::taobaoToken($code);
            if (!$result) {
                $this->error('授权失败, 请关闭该页面重新点击授权', '');
            }

            if ($result['code'] !== 200) {
                $this->error($result['error_description'] . ', 请关闭该页面重新点击授权', '');
            }

            $access_token = $result['access_token'];
            $service = new TkPid();
            $res = $service->createTaoPid($user, $access_token);
            if($res == false) {
                $this->error($service->getError() . ', 请关闭该页面重新点击授权', '');
            }
        }

        $this->success('授权成功, 请关闭该页面', '');
    }


    public function register($rec_mobile = '')
    {
        $pinfo = \app\common\model\User::get(['mobile' => $rec_mobile]);

        $this->assign('pinfo', $pinfo);
        return $this->fetch();
    }
}