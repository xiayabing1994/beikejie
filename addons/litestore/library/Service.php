<?php

namespace addons\litestore\library;

use addons\third\model\Third;
use app\common\model\User;
use fast\Random;
use think\Debug;
use think\Exception;
use think\Log;

/**
 * 第三方登录服务类
 *
 * @author Karson
 */
class Service
{
    /**
     * 第三方登录 ByHawk
     * @param string    $platform   平台
     * @param array     $params     参数
     * @param int       $keeptime   有效时长
     * @return boolean
     */
    public static function connect_hawk($platform, $params = [],$extend, $keeptime = 0)
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
        $auth = \app\common\library\Auth::instance();

        $auth->keeptime($keeptime);
        $third = Third::get(['platform' => $platform, 'openid' => $params['openid']]);
        if ($third)
        {
            $user = User::get($third['user_id']);
            if (!$user)
            {
                //如果没有找到用户，证明已经呗管理员删去了，那么自动增加
                $username = Random::alnum(20);
                $password = Random::alnum(6);
                // 默认注册一个会员
                $result = $auth->register($username, $password, $username . '@217dan.com', '', $extend, $keeptime);
                if (!$result)
                {
                    return FALSE;
                }
                $user = $auth->getUser();
                $fields = ['username' => 'u' . $user->id, 'email' => 'u' . $user->id . '@217dan.com'];
                if (isset($params['userinfo']['nickname']))
                    $fields['nickname'] = $params['userinfo']['nickname'];
                if (isset($params['userinfo']['avatar']))
                    $fields['avatar'] = $params['userinfo']['avatar'];
                $fields['group_id'] = 1;
                // 更新会员资料
                $user->save($fields);

                // 反过来更新 第三方的表
                $third['user_id'] =  $user->id;
            }
            $third->save($values);
            return $auth->direct($user->id);
        }
        else
        {
            // 先随机一个用户名,随后再变更为u+数字id
            $username = Random::alnum(20);
            $password = Random::alnum(6);
            // 默认注册一个会员
            $result = $auth->register($username, $password, '', '', $extend, $keeptime);
            if (!$result)
            {
                return FALSE;
            }
            $user = $auth->getUser();
            $fields = ['username' => 'u' . $user->id];
            if (isset($params['userinfo']['nickname']))
                $fields['nickname'] = $params['userinfo']['nickname'];
            if (isset($params['userinfo']['avatar']))
                $fields['avatar'] = $params['userinfo']['avatar'];
            $fields['group_id'] = 1;
            // 更新会员资料
            $user->save($fields);

            // 保存第三方信息
            $values['user_id'] = $user->id;
            $values['platform'] = $platform;
            Third::create($values);

            // 写入登录Cookies和Token
            return $auth->direct($user->id);
        }
    }
}
