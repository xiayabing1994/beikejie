<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\admin\model\Article;
/**
 * 首页接口
 */
class Index extends Api
{
    protected $noNeedLogin = ['*'];
    protected $noNeedRight = ['*'];

    /**
     * 首页
     *
     */
    public function index()
    {
        $this->success('请求成功');
    }
    public function article_list()
    {
        $type=$this->request->param('type',20);
        $page = $this->request->param('page', 1);
        $limit = $this->request->param('limit', 8);
        $list = Article::field('id, name, desc, createtime')
            ->where(['type' => "$type", 'status' => '1'])
            ->order('id desc')
            ->page($page, $limit)
            ->select();

        $this->success('文章列表', $list);
    }

// 文章详情
    public function article_detail()
    {
        $id=$this->request->param('id');
        $info = Article::get($id);

        if(empty($info)) $this->error('该文章不存在');

        $this->success('文章详情', $info);
    }
}
