<?php

return [
    'controllers' => [
        'invokables' => [
            'Question\Controller\Question' => 'Question\Controller\QuestionController',
            'Question\Controller\Listquest' => 'Question\Controller\ListquestController',
            'Question\Controller\Admin' => 'Question\Controller\AdminController',
            'Question\Controller\Premium' => 'Question\Controller\PremiumController',
            'Question\Controller\User' => 'Question\Controller\UserController',
        ],
    ],
    'router' => [
        'routes' => [
            'question' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/question',
                    'defaults' => [
                        'controller' => 'Question\Controller\Question'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'learn' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/learn[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ],                              
                            'defaults' => [
                                'action'     => 'index',
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/add[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ], 
                            'defaults' => [
                                'action'     => 'add',
                            ],
                        ],
                    ],
                    'edit' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/edit[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ],  
                            'defaults' => [
                                'action'     => 'edit',
                            ],
                        ],
                    ],              
                    'delete' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/delete[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ],  
                            'defaults' => [
                                'action'     => 'delete',
                            ],
                        ],
                    ],              
                    'user' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/user[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ],  
                            'defaults' => [
                                'controller' => 'Question\Controller\User',
                                'action'     => 'index',
                            ],
                        ],
                    ],              
                ],
            ],
            'list' => [
                'type'    => 'literal',
                'options' => [
                    'route'    => '/list',
                    'defaults' => [
                        'controller' => 'Question\Controller\Listquest',
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'paginator' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/page[/:page]',
                            'constraints' => [
                                'page'     => '[0-9]+',
                            ],                    
                            'defaults' => [
                                'page'     => 1,
                            ],
                        ],
                    ],
                    'add' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/add',
                            'defaults' => [
                                'action'     => 'add',
                            ],
                        ],
                    ],                    
                    'show' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/show[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ],  
                            'defaults' => [
                                'action'     => 'show',
                            ],
                        ],
                    ],                    
                    'rate' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/rate[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ],  
                            'defaults' => [
                                'action'     => 'rate',
                            ],
                        ],
                    ],    
                    'delete' => [
                        'type' => 'segment',
                        'options' => [
                            'route' => '/delete[/:id]',
                            'constraints' => [
                                'id'     => '[0-9]+',
                            ],  
                            'defaults' => [
                                'action'     => 'delete',
                            ],
                        ],
                    ],    
                    'premium' => [
                        'type' => 'literal',
                        'options' => [
                            'route' => '/premium',
                            'defaults' => [
                                'controller' => 'Question\Controller\Premium',
                                'action'     => 'index',
                            ],
                        ],
                    ],    
                ],
            ],
            'zfcadmin' => [
                'child_routes' => array(
                    'lists' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/lists',
                            'defaults' => array(
                                'controller' => 'Question\Controller\Admin',
                                'action'     => 'index',
                            ),
                        ),
//                        'child_routes' =>array(
//                            'lists' => array(
//                                'type' => 'literal',
//                                'options' => array(
//                                    'route' => '/',
//                                    'defaults' => array(
//                                        'controller' => 'mycontroller',
//                                        'action'     => 'myaction',
//                                    ),
//                                ),
//                            ),
//                        ),
                    ),
                ),
            ],
        ],
    ],
    'view_manager' => [
       'template_path_stack' => [
            'question' => __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'grid' => 'Question\View\Helper\Grid',
        ],
    ],
    'doctrine' => [
        'driver' => [
            // defines an annotation driver with two paths
            'Question_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\XMLDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/xml/Question'
                ],
            ],
            // default metadata driver, aggregates all other drivers into a single one.
            // Override `orm_default` only if you know what you're doing
            'orm_default' => [
                'drivers' => [
                    // register `my_annotation_driver` for any entity under namespace `My\Namespace`
                    'Question\Entity' => 'Question_driver'
                ]
            ]
        ],
    ],
    'service_manager' => [
        'factories' => [
            
        ],
    ],
];
