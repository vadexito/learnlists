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
    'db' => [
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=learnlists_local;host=localhost',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter'
                    => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
    ],
    'navigation' => [
         'default' => [
             [
                 'id' => 'home',
                 'label' => 'Learnlists',
                 'route' => 'home',
                 'class' => 'brand'
             ],
             [
                 'id' => 'list_show',
                 'label' => 'Learn',
                 'route' => 'list'
             ],
             [
                 'id' => 'list_create',
                 'label' => 'New list',
                 'route' => 'list',
             ],
             [
                 'id' => 'login',
                 'label' => 'Sign In',
                 'route' => 'zfcuser/login',
             ],
             [
                 'id' => 'register',
                 'label' => 'Sign Up',
                 'route' => 'zfcuser/register',
             ],
             [
                 'id' => 'logout',
                 'label' => 'Log out',
                 'route' => 'zfcuser/logout',
             ],
             [
                 'id' => 'profile',
                 'label' => '',
                 'route' => 'home',
             ],
         ]
     ]
];
