<?php

function menuData()
{
    return [
        (object)[
            'menu' => [
                (object)[
                    'name' => 'Dashboard',
                    'icon' => 'mdi mdi-view-dashboard-outline mdi-24px',
                    'slug' => 'dashboard',
                    'url'  => route('dashboard'),
                ],
                (object)[
                    'name' => 'Tiang Lampu',
                    'icon' => 'mdi-lamps-outline mdi-24px',
                    'slug' => 'poles',
                    'url'  => route('admin.poles.index'),
                    'active_on' => [
                        'admin/poles',
                        'admin/poles/*',
                    ]
                ],
                (object)[
                    'name' => 'Lampu',
                    'icon' => 'mdi-lightbulb-on-outline mdi-24px',
                    'slug' => 'lampus',
                    'url'  => route('admin.lampus.index'),
                    'active_on' => [
                        'admin/lampus',
                        'admin/lampus/*',
                    ]
                ],
                (object)[
                    'name' => 'CCTV',
                    'icon' => 'mdi mdi-cctv mdi-24px',
                    'slug' => 'cctvs',
                    'url'  => route('admin.cctvs.index'),
                    'active_on' => [
                        'admin/cctvs',
                        'admin/cctvs/*',
                    ]
                ],
                (object)[
                    'name' => 'IoT',
                    'icon' => 'mdi mdi-access-point-network mdi-24px',
                    'slug' => 'iots',
                    'url'  => route('admin.iots.index'),
                    'active_on' => [
                        'admin/iots',
                        'admin/iots/*',
                    ]
                ],
                (object)[
                    'name' => 'User Management',
                    'icon' => 'mdi mdi-account-multiple-outline mdi-24px',
                    'slug' => 'users',
                    'url'  => route('admin.users.index'),
                    'active_on' => [
                        'admin/users',
                        'admin/users/*',
                    ]
                ],
            ]
        ]
    ];
}
