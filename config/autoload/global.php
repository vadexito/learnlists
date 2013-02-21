<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */


return [
//    'db' => [
//        'driver'         => 'Pdo',
//        'dsn'            => 'mysql:dbname=learnlists_local;host=localhost',
//        'driver_options' => [
//            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
//        ],
//    ],
    
    'navigation' => [
        'default' => [
            [
                'id' => 'general',
                'uri' => '#',
                'pages' => [
                    [
                        'id' => 'home',
                        'label' => 'Learnlists',
                        'route' => 'home',
                        'class' => 'brand',                        
                    ],
                    [
                        'id' => 'list_show',
                        'label' => 'Browse',
                        'route' => 'list',
                        'action' => 'index',
                    ],
                    [
                        'id' => 'list_create',
                        'label' => 'New list',
                        'route' => 'list',
                        'action' => 'add',
                        'resource' => 'listquest',
                        'privilege' => 'add',
                    ],
                ],
            ],             
            [
                'id' => 'user',
                'uri' => '#',
                'pages' => [
                    [
                        'id' => 'login',
                        'label' => 'Sign In',
                        'route' => 'zfcuser/login',
                        'resource' => 'user',
                        'privilege' => 'login',
                    ],
                    [
                        'id' => 'register',
                        'label' => 'Sign Up',
                        'route' => 'zfcuser/register',
                        'resource' => 'user',
                        'privilege' => 'register',
                    ],
                    [
                        'id' => 'profile',
                        'label' => '',
                        'uri' => '#',
                    ],
                    [
                        'id' => 'logout',
                        'label' => 'Log out',
                        'route' => 'zfcuser/logout',
                        'resource' => 'user',
                        'privilege' => 'logout',
                    ],
                    
                ],
            ],
        ],
    ],
];
