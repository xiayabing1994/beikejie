<?php
namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\litestore\library\Service;
use addons\third\model\Third;
use app\common\library\Auth;
use fast\Http;
use think\Config;
use think\Validate;


class User extends Api
{
	protected $noNeedLogin = ['login_hawk','Updata_user_hawk'];

	protected $token = '';

    public function _initialize()
    {

        $this->token = $this->request->post('token');
        if ($this->request->action() == 'login' && $this->token) {
            $this->request->post(['token' => '']);
        }
        parent::_initialize();

        if (!Config::get('fastadmin.usercenter')) {
            $this->error(__('User center already closed'));
        }

        $ucenter = get_addon_info('ucenter');
        if ($ucenter && $ucenter['state']) {
            include ADDON_PATH . 'ucenter' . DS . 'uc.php';
        }

    }

    public function Updata_user_hawk(){
        $userInfo = $this->request->post("userInfo");
        $mobile = $this->request->post("mobile");
        if (!$userInfo||!$this->token) {
            $this->error("参数不正确");
        }

        $this->auth->init($this->token);
        //检测是否登录
        if ($this->auth->isLogin()) {
            $user = $this->auth->getUser();
            $fields = [];
            $userInfo = json_decode($userInfo,true);
            $fields['avatar'] = $userInfo['avatarUrl'];
            $fields['nickname'] = $userInfo['nickName'];
            $fields['mobile'] = $mobile;
            $user->save($fields);
            $this->success("已经登录", ['userInfo' => $this->auth->getUserinfo()] );
        }else{
            $this->error("未登录状态");
        }
    }

    public function login_hawk()
    {
        $config = get_addon_config('litestore');
        $code = $this->request->post("code");
        if (!$code) {
            $this->error("参数不正确");
        }

        $params = [
            'appid'      => $config['AppID'],
            'secret'     => $config['AppSecret'],
            'js_code'    => $code,
            'grant_type' => 'authorization_code'
        ];
        $result = Http::sendRequest("https://api.weixin.qq.com/sns/jscode2session", $params, 'GET');
        if ($result['ret']) {
            $json = (array)json_decode($result['msg'], true);
            if (isset($json['openid'])) {
                //如果有传Token
                if ($this->token) {
                    $this->auth->init($this->token);
                    //检测是否登录
                    if ($this->auth->isLogin()) {
                        $third = Third::where(['openid' => $json['openid'], 'platform' => 'wxapp'])->find();
                        if ($third && $third['user_id'] == $this->auth->id) {
                            //把最新的 session_key 保存到 第三方表的 access_token 里
                            $third['access_token'] = $json['session_key'];
                            $third->save();
                            $this->success("登录成功", $this->Format_avatarUrl($this->auth->getUserinfo()));
                        }
                    }
                }

                $platform = 'wxapp';
                $result = [
                    'openid'        => $json['openid'],
                    'userinfo'      => [
                        'nickname' => '游客未登录',
                    ],
                    'access_token'  => $json['session_key'],
                    'refresh_token' => '',
                    'expires_in'    => isset($json['expires_in']) ? $json['expires_in'] : 0,
                ];
                $extend = ['mobile'=>'NoLoginData' ,'gender' => '0', 'nickname' => '游客未登录', 'avatar' =>'/assets/img/avatar.png'];
                $ret = Service::connect_hawk($platform, $result, $extend);
                if ($ret) {
                    $auth = Auth::instance();
                    $this->success("登录成功", $this->Format_avatarUrl($this->auth->getUserinfo()));
                } else {
                    $this->error("连接失败");
                }
            } else {
                $this->error("登录失败",$json);
            }
        }

        return;
    }

     private function startsWith($str, $prefix)
    {
        for ($i = 0; $i < strlen($prefix); ++$i) {
            if ($prefix[$i] !== $str[$i]) {
                return false;
            }
        }
        return true;
    }
    private function Format_avatarUrl($userInfo){
        $avatar = $userInfo['avatar'];
        if($this->startsWith($avatar,"/uploads/")){
            $userInfo['avatar'] = cdnurl($avatar, true);
        }
        return ['userInfo' => $userInfo];
    }
}