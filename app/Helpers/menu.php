<?php

if (!function_exists('menuData')) {
    function menuData()
    {
        return [
            (object)[
                'menu' => [
                    (object)[
                        'name' => 'Dashboard',
                        'icon' => 'mdi-home-outline',
                        'url'  => route('dashboard')
                    ],
                    (object)[
                        'name' => 'Users',
                        'icon' => 'mdi-account-outline',
                        'url'  => route('users.index')
                    ]
                ]
            ]
        ];
    }
}
