<?php

function menuData()
{
    return [
        (object)[
            'menu' => [
                (object)[
                    'name' => 'Dashboard',
                    'icon' => 'mdi-home-outline',
                    'url'  => route('dashboard'),
                    'slug' => 'dashboard',
                ],
                (object)[
                    'name' => 'Lampu',
                    'icon' => 'mdi-lightbulb-on-outline',
                    'slug' => 'lampu',
                    'url'  => '/lampu',
                    'active_on' => [
                        'lampu',
                        'admin/lampus/create',
                        'admin/lampus/edit',
                    ]
                ],
                (object)[
                    'name' => 'Tiang Lampu',
                    'icon' => 'mdi-floor-lamp',
                    'slug' => 'tiang-lampu',
                    'url'  => '/tiang-lampu',
                    'active_on' => [
                        'tiang-lampu',
                        'admin/poles/create',
                        'admin/poles/edit',
                    ]
                ],
            ]
        ]
    ];
}
