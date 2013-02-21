<?php

return [
    'controllers' => [
        'invokables' => [
            'Question\Controller\Question' => 'Question\Controller\QuestionController',
            'Question\Controller\Listquest' => 'Question\Controller\ListquestController',
        ],
    ],
    'router' => [
        'routes' => [
            'question' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/question[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Question\Controller\Question',
                        'action'     => 'index',
                    ],
                ],
            ],
            'list' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/list[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Question\Controller\Listquest',
                        'action'     => 'index',
                    ],
                ],
            ],
            'zfcadmin' => array(
                'child_routes' => array(
                    'mymodule' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/application',
                            'defaults' => array(
                                'controller' => 'Question\Controller\AdminController',
                                'action'     => 'index',
                            ),
                        ),
                        'child_routes' =>array(
                            'mychildroute' => array(
                                'type' => 'literal',
                                'options' => array(
                                    'route' => '/',
                                    'defaults' => array(
                                        'controller' => 'mycontroller',
                                        'action'     => 'myaction',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ],
    ],
    'view_manager' => [
       'template_path_stack' => [
            'question' => __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'replaceBlank' => 'Question\View\Helper\ReplaceBlank',
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
