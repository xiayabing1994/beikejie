<?php

namespace addons\litestore;

use app\common\library\Menu;
use think\Addons;

/**
 * 插件
 */
class Litestore extends Addons
{

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
        $menu = [
            [
               'name'    => 'litestore',
               'title'   => '移动端商城',
               'icon'    => 'fa fa-shopping-basket',
               'sublist' => [
                                [
                                    'name'    => 'litestorenews',
                                    'title'   => '首页banner',
                                    'icon'    => 'fa fa-image',
                                    'sublist' => [
                                        ['name' => 'litestorenews/index', 'title' => '查看'],
                                        ['name' => 'litestorenews/add', 'title' => '添加'],
                                        ['name' => 'litestorenews/edit', 'title' => '修改'],
                                        ['name' => 'litestorenews/del', 'title' => '删除'],
                                        ['name' => 'litestorenews/multi', 'title' => '批量更新'],
                                    ]
                                ],
                                [
                                    'name'    => 'litestoregoods',
                                    'title'   => '商品设置',
                                    'icon'    => 'fa fa-gift',
                                    'sublist' => [
                                        ['name' => 'litestoregoods/index', 'title' => '查看'],
                                        ['name' => 'litestoregoods/add', 'title' => '添加'],
                                        ['name' => 'litestoregoods/edit', 'title' => '修改'],
                                        ['name' => 'litestoregoods/del', 'title' => '删除'],
                                        ['name' => 'litestoregoods/multi', 'title' => '批量更新'],
                                        ['name' => 'litestoregoods/addSpec', 'title' => '增加规格'],
                                        ['name' => 'litestoregoods/addSpecValue', 'title' => '增加规格值'],
                                    ]
                                ],
                                [
                                    'name'    => 'litestorecategory',
                                    'title'   => '商品分类',
                                    'icon'    => 'fa fa-th',
                                    'sublist' => [
                                        ['name' => 'litestorecategory/index', 'title' => '查看'],
                                        ['name' => 'litestorecategory/add', 'title' => '添加'],
                                        ['name' => 'litestorecategory/edit', 'title' => '修改'],
                                        ['name' => 'litestorecategory/del', 'title' => '删除'],
                                        ['name' => 'litestorecategory/multi', 'title' => '批量更新'],
                                    ]
                                ],
                                [
                                    'name'    => 'litestorefreight',
                                    'title'   => '运费模板设置',
                                    'icon'    => 'fa fa-train',
                                    'sublist' => [
                                        ['name' => 'litestorefreight/index', 'title' => '查看'],
                                        ['name' => 'litestorefreight/add', 'title' => '添加'],
                                        ['name' => 'litestorefreight/edit', 'title' => '修改'],
                                        ['name' => 'litestorefreight/del', 'title' => '删除'],
                                        ['name' => 'litestorefreight/multi', 'title' => '批量更新'],
                                    ]
                                ],
                                [
                                    'name'    => 'litestoreorder',
                                    'title'   => '订单管理',
                                    'icon'    => 'fa fa-tasks',
                                    'sublist' => [
                                        ['name' => 'litestoreorder/index', 'title' => '查看'],
                                        ['name' => 'litestoreorder/add', 'title' => '添加'],
                                        ['name' => 'litestoreorder/edit', 'title' => '修改'],
                                        ['name' => 'litestoreorder/del', 'title' => '删除'],
                                        ['name' => 'litestoreorder/multi', 'title' => '批量更新'],
                                        ['name' => 'litestoreorder/detail', 'title' => '订单详情'],
                                    ]
                                ],
                             ]
            ]
        ];
        Menu::create($menu);        
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        Menu::delete('litestore');
        return true;
    }

    /**
     * 插件启用方法
     * @return bool
     */
    public function enable()
    {
        Menu::enable('litestore');
        return true;
    }

    /**
     * 插件禁用方法
     * @return bool
     */
    public function disable()
    {
        Menu::disable('litestore');
        return true;
    }

    public function GetCfg(){
        return $this->getConfig();
    }

}
