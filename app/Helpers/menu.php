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
                    'name' => 'Users',
                    'url'  => '/users',
                    'slug' => 'users.index',
                ]
            ]
        ]
    ];
}
