<?php

function menuData()
{
    $prefixUrl = 'admin';

    return [
        (object)[
            'menu' => [
                (object)[
                    'name' => 'Dashboard',
                    'icon' => 'mdi mdi-view-dashboard-outline mdi-24px',
                    'url'  => route('dashboard'),
                    'slug' => 'dashboard',
                ],
                (object)[
                    'name' => 'Tiang Lampu',
                    'icon' => 'mdi-lamps-outline mdi-24px',
                    'slug' => 'poles',
                    'url'  => $prefixUrl . '/poles',
                    'active_on' => [
                        'poles',
                        'admin/poles/',
                        'admin/poles/*',
                    ]
                ],
                (object)[
                    'name' => 'Lampu',
                    'icon' => 'mdi-lightbulb-on-outline mdi-24px',
                    'slug' => 'lampu',
                    'url'  => $prefixUrl . '/lampu',
                    'active_on' => [
                        'lampu',
                        'admin/lampus/',
                        'admin/lampus/*',
                    ]
                ],
                (object)[
                    'name' => 'CCTV',
                    'icon' => 'mdi mdi-cctv mdi-24px',
                    'slug' => 'cctv',
                    'url'  => $prefixUrl . '/cctv',
                    'active_on' => [
                        'cctv',
                        'admin/cctv/',
                        'admin/cctv/*',
                    ]
                ],
                (object)[
                    'name' => 'IoT',
                    'icon' => 'mdi mdi-access-point-network mdi-24px',
                    'slug' => 'iot',
                    'url'  => $prefixUrl . '/iot',
                    'active_on' => [
                        'iot',
                        'admin/iot/',
                        'admin/iot/*',
                    ]
                ],
                (object)[
                    'name' => 'User Management',
                    'icon' => 'mdi mdi-access-point-network mdi-24px',
                    'slug' => 'user',
                    'url'  => $prefixUrl . '/user',
                    'active_on' => [
                        'user',
                        'admin/user/',
                        'admin/user/*',
                    ]
                ],
            ]
        ]
    ];
}
