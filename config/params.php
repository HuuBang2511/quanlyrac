<?php

return [
    'adminEmail' => 'hcdc.ythd@gmail.com',
    'senderEmail' => 'noreply@hcdcythd.com',
    'senderName' => 'GIS Cap nuoc',
    'bsVersion' => '4.x',
    'bsDependencyEnabled' => false,
    'center' => [
        'lat' => 10.820056203215767,
        'lng' => 106.79050235703482,
    ],
    'siteName' => 'Hệ thống quản lý rác',
    'loginPage' => [
        'logoUrl' => '/resources/images/logocamau.jpg',
        'backgroundUrl' => '/resources/images/banner_bg.jpg'
    ],
    'adminSidebar' => [[
        'name' => 'Quản trị hệ thống',
        'items' => [
            [
                'name' => 'Quản lý người dùng',
                'icon' => 'fa-users',
                'url' => '/user/auth-user'
            ],
            [
                'name' => 'Quản lý nhóm quyền',
                'icon' => 'fa-th-list',
                'url' => '/user/auth-group'
            ],
            [
                'name' => 'Quản lý quyền truy cập',
                'icon' => 'fa-th-list',
                'url' => '/user/auth-role'
            ],
            [
                'name' => 'Quản lý hoạt động',
                'icon' => 'fa-th-list',
                'url' => '/user/auth-action'
            ],
        ],
    ]]
];
