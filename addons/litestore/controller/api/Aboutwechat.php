<?php
namespace addons\litestore\controller\api;

use app\common\controller\Api;
use addons\third\model\Third;
use EasyWeChat\Foundation\Application;

class Aboutwechat extends Api
{
	protected $noNeedRight = ['*'];

    public function get_PhoneNum(){
        $encryptedData = $this->request->post("encryptedData");
        $iv = $this->request->post("iv");

        $third = Third::where(['user_id' => $this->auth->id, 'platform' => 'wxapp'])->find();
        $sessionKey = $third['access_token'];

        $config = get_addon_config('litestore');
        $options = [
                    //'debug'  => true,
                    'mini_program' => [
                        'app_id'   => $config['AppID'],
                        'secret'   => $config['AppSecret'],
                        'token'    => 'component-token',
                        'aes_key'  => 'component-aes-key'
                        ],
                    ];
        $app = new Application($options);
        $this->miniProgram = $app->mini_program;

        $this->success('', $this->miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptedData));
    }

}
