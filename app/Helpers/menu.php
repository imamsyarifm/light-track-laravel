<?php

function menuData()
{
    return [
        (object)[
            'menu' => [
                (object)[
                    'name' => 'Dashboard',
                    'icon' => 'mdi-home-outline',
                    'url'  => route('dashboard-analytics'),
                    'slug' => 'dashboard-analytics',
                ],
                (object)[
                    'name' => 'Users',
                    'icon' => 'mdi-account-outline',
                    'url'  => route('users.index'),
                    'slug' => 'users',
                ]
            ]
        ]
    ];
}
