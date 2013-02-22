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

// in order to be detected by poedit
function _($a,$b=NULL){return $a;}


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
                        'route' => 'list',
                        'action' => 'index',
                    ],
                    [
                        'id' => 'list_create',
                        'label' => _('New list'),
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
                    [
                        'id' => 'profile',
                        'label' => '',
                        'uri' => '#',
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
    ],
];
