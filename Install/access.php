<?php
declare(strict_types=1);
// 菜单层级最多三级
//[
//    [
//        'parent_access_id' => 0,
//        'access_name' => '示例',
//        'uri' => 'demo/demo/none',
//        'params' => '',
//        'sort' => 100,
//        'is_menu' => 1,
//        'menu_icon' => 'el-icon-data-analysis',
//        'children' => []
//    ]
//]
return [
    [
        'parent_access_id' => 0,
        'access_name' => '用户管理',
        'uri' => 'user/user/none',
        'params' => '',
        'sort' => 100,
        'is_menu' => 1,
        'menu_icon' => 'line-icon-geren2',
        'children' => [
            [
                'access_name' => '用户列表',
                'uri' => 'user/user/index',
                'sort' => 100,
                'is_menu' => 1,
            ],
            [
                'access_name' => '收益记录',
                'uri' => 'user/sharereward/index',
                'sort' => 100,
                'is_menu' => 1,
            ],
            [
                'access_name' => '提现记录',
                'uri' => 'user/withdraw/index',
                'sort' => 100,
                'is_menu' => 1,
            ]
        ]
    ]
];