<?php
/**
 * Created by PhpStorm.
 * User: PHPer
 * Date: 2019/4/17
 * Time: 15:56
 */

namespace app\api\behavior;


class CORS
{
    public function run(&$params)
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        $allow_origin = array(
            'http://localhost',
            'http://localhost:8100',
        );

        if (in_array($origin, $allow_origin)) {
            header('Access-Control-Allow-Origin:' . $origin);
            header("Access-Control-Allow-Credentials:true");
            header("Access-Control-Allow-Headers:Origin,X-Requested-With,Content-Type,Token");
            header('Access-Control-Allow-Methods:GET,POST,PUT,OPTIONS');
        }
        if(request()->isOptions()){
            exit();
        }
    }
}