<?php

function menuData()
{
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
                    'name' => 'Lampu',
                    'icon' => 'mdi-lightbulb-on-outline mdi-24px',
                    'slug' => 'lampu',
                    'url'  => '/lampu',
                    'active_on' => [
                        'lampu',
                        'admin/lampus/',
                        'admin/lampus/*',
                    ]
                ],
                (object)[
                    'name' => 'Tiang Lampu',
                    'icon' => 'mdi-lamps-outline mdi-24px',
                    'slug' => 'tiang-lampu',
                    'url'  => '/tiang-lampu',
                    'active_on' => [
                        'tiang-lampu',
                        'admin/poles/',
                        'admin/poles/*',
                    ]
                ],
                (object)[
                    'name' => 'CCTV',
                    'icon' => 'mdi mdi-cctv mdi-24px',
                    'slug' => 'cctv',
                    'url'  => '/cctv',
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
                    'url'  => '/iot',
                    'active_on' => [
                        'iot',
                        'admin/iot/',
                        'admin/iot/*',
                    ]
                ],
            ]
        ]
    ];
}
