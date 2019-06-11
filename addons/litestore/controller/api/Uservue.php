<?php

namespace addons\litestore\controller\api;
use app\common\controller\Api;

use addons\third\library\Application;
use addons\third\library\Service;
use think\Cookie;
use think\Hook;
use think\Session;
use addons\third\model\Third;
use app\common\model\User;
use think\Db;
use think\exception\PDOException;



class Uservue extends Api
{
	protected $noNeedLogin = ['callback','connect'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    	$config = get_addon_config('third');
        if (!$config)
        {
            $this->error(__('Invalid parameters'));
        }
        $options = array_intersect_key($config, array_flip(['qq', 'weibo', 'wechat']));
        foreach ($options as $k => &$v)
        {
            $v['callback'] = addon_url('litestore/api.uservue/callback', [':platform' => $k], false, true);
            $options[$k] = $v;
        }
        unset($v);
        $this->app = new Application($options);
    }
    

	public function index()
    {
        $third_data = Third::where('user_id',$this->auth->id)->find();
        $this->success('', [
                                'user' => $this->auth->getUser(),
                                'third' => $third_data
                          ]);
    }

    /**
     * 发起授权
     */
    public function connect()
    {
        $platform = $this->request->param('platform');
        $url = $this->request->param('url');
        if (!$this->app->{$platform}) {
            $this->error(__('Invalid parameters'));
        }
        if ($url) {
            Session::set("redirecturl", $url);
        }
        // 跳转到登录授权页面
        $AuthorizeUrl = $this->app->{$platform}->getAuthorizeUrl();
        header('Location: '.$AuthorizeUrl);
        return;
    }

    /**
     * 通知回调
     */
    public function callback()
    {
        $auth = $this->auth;

        //监听注册登录注销的事件
        Hook::add('user_login_successed', function ($user) use ($auth) {
            $expire = input('post.keeplogin') ? 30 * 86400 : 0;
            Cookie::set('uid', $user->id, $expire);
            Cookie::set('token', $auth->getToken(), $expire);
        });
        Hook::add('user_register_successed', function ($user) use ($auth) {
            Cookie::set('uid', $user->id);
            Cookie::set('token', $auth->getToken());
        });
        Hook::add('user_logout_successed', function ($user) use ($auth) {
            Cookie::delete('uid');
            Cookie::delete('token');
        });
        $platform = $this->request->param('platform');
        if(!$platform){
            $platform = 'wechat';
        }
        // 成功后返回之前页面
        $url = Session::has("redirecturl") ? Session::pull("redirecturl") : url('index/user/index');

        // 授权成功后的回调
        $result = $this->app->{$platform}->getUserInfo();
        if ($result) {
            //这里根据 本身是否存在用户登录的情况  如果用户登录没有登录微信 那么自动绑定流程  如果没有登录，那么先注册账号 然后再登录
            if($this->auth->getUser()){
                $loginret = $this->bind_connect($platform, $result);
            }else{
                $loginret = Service::connect($platform, $result);
            }

            if ($loginret) {
                $synchtml = '';
                ////////////////同步到Ucenter////////////////
                if (defined('UC_STATUS') && UC_STATUS) {
                    $uc = new \addons\ucenter\library\client\Client();
                    $synchtml = $uc->uc_user_synlogin($this->auth->id);
                }
                $UrlSetCookie = explode("/octothorpe",$url)[0];
                $UrlSetCookie = explode("?",$UrlSetCookie)[0] . '#/SetToken';

                header('Location: '.$UrlSetCookie.'?token='.$auth->getToken().'&url='.urlencode($url));
            }
        }
        $this->error(__('操作失败'), $url);
    }

    private function bind_connect($platform, $params = [], $extend = [], $keeptime = 0)
    {
        $time = time();
        $values = [
            'platform'      => $platform,
            'openid'        => $params['openid'],
            'openname'      => isset($params['userinfo']['nickname']) ? $params['userinfo']['nickname'] : '',
            'access_token'  => $params['access_token'],
            'refresh_token' => $params['refresh_token'],
            'expires_in'    => $params['expires_in'],
            'logintime'     => $time,
            'expiretime'    => $time + $params['expires_in'],
        ];

        $third = Third::get(['platform' => $platform, 'openid' => $params['openid']]);
        if ($third) {
            $user = User::get($third['user_id']);
            if (!$user) {
                return FALSE;
            }
            $third->save($values);
            return TRUE;
        } else {
            Db::startTrans();
            try {
                $user = $this->auth->getUser();
                $fields = ['email' => 'u' . $user->id . '@fastadmin.net'];
                if (isset($params['userinfo']['nickname']))
                    $fields['nickname'] = $params['userinfo']['nickname'];
                if (isset($params['userinfo']['avatar']))
                    $fields['avatar'] = $params['userinfo']['avatar'];

                // 更新会员资料
                $user = User::get($user->id);
                $user->save($fields);

                // 保存第三方信息
                $values['user_id'] = $user->id;
                Third::create($values);
                Db::commit();
            } catch (PDOException $e) {
                Db::rollback();
                $this->auth->logout();
                return FALSE;
            }

            return TRUE;
        }
    }

}