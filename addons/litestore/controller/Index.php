<?php

namespace addons\litestore\controller;

use think\addons\Controller;

class Index extends Controller
{

    public function index()
    {
        return $this->fetch('vue-mobile'); 
    }

}
