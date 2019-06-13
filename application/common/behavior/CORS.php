<?php
/**
 * Created by PhpStorm.
 * User: PHPer
 * Date: 2019/4/17
 * Time: 15:56
 */

namespace app\common\behavior;


class CORS
{
    public function run(&$params)
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: POST,GET,OPTIONS');
        if (request()->isOptions()) {
            exit();
        }
    }
}