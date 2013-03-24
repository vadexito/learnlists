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
    'navigation' => [
        'default' => [
            [
                'id' => 'general',
                'uri' => '#',
                'pages' => [
                    [
                        'id' => 'home',
                        'label' => _('Learnlists'),
                        'route' => 'home',
                        'class' => 'brand',                        
                    ],
                    [
                        'id' => 'list_show',
                        'label' => _('Browse'),
                        'route' => 'list'
                    ],
                    [
                        'id' => 'list_create',
                        'label' => _('New list'),
                        'route' => 'list/add',
                        'resource' => 'listquest',
                        'privilege' => 'add',
                    ],  
                    [
                        'id' => 'admin-lists',
                        'label' => 'Admin',
                        'route' => 'zfcadmin',
                        'resource' => 'admin',
                    ],
                    [
                        'id' => 'premium-myprogram',
                        'label' => _('My program'),
                        'route' => 'list/premium',
                        'resource' => 'premium',
                    ],
                    
                ],
            ],             
            [
                'id' => 'user',
                'uri' => '#',
                'pages' => [
                    [
                        'id' => 'login',
                        'label' => _('Sign In'),
                        'route' => 'zfcuser/login',
                        'resource' => 'user',
                        'privilege' => 'login',
                    ],
                    [
                        'id' => 'register',
                        'label' => _('Sign Up'),
                        'route' => 'zfcuser/register',
                        'resource' => 'user',
                        'privilege' => 'register',
                    ],
                ],
            ],
            [
                'id' => 'profile',
                'uri' => '#',
                'resource' => 'user',
                'privilege' => 'account',
                'pages' => [
                    [
                        'id' => 'user_account',
                        'label' => _('My account'),
                        'route' => 'question/user',
                        'resource' => 'user',
                        'privilege' => 'account',
                    ],
                    [
                        'id' => 'logout',
                        'label' => _('Log out'),
                        'route' => 'zfcuser/logout',
                        'resource' => 'user',
                        'privilege' => 'logout',
                    ],
                ],
            ],
        ],
        'admin' => [
            'lists' => [
                'label' => 'Lists of questions',
                'route' => 'zfcadmin/lists',
            ],
            'home' => [
                'label' => 'Learnlists',
                'route' => 'home',
            ],
        ],
    ],    
];
