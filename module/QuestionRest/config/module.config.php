<?php

return [
    'controllers' => [
        'invokables' => [
            'QuestionRest\Controller\QuestionRest' => 'QuestionRest\Controller\QuestionRestController',
            'QuestionRest\Controller\ListquestRest' => 'QuestionRest\Controller\ListquestRestController',
            'QuestionRest\Controller\QuestionresultRest' => 'QuestionRest\Controller\QuestionresultRestController',
            'QuestionRest\Controller\RoundRest' => 'QuestionRest\Controller\RoundRestController',
        ],
    ],
    'router' => [
        'routes' => [
            'question-rest' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/question-rest[/:id]',
                    'constraints' => [
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'QuestionRest\Controller\QuestionRest'
                    ],
                ],
            ],
            'listquest-rest' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/listquest-rest[/:id]',
                    'constraints' => [
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'QuestionRest\Controller\ListquestRest'
                    ],
                ],
            ],
            'round-rest' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/round-rest[/:id]',
                    'constraints' => [
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'QuestionRest\Controller\RoundRest'
                    ],
                ],
            ],
            'questionresult-rest' => [
                'type'    => 'segment',
                'options' => [
                    'route'    => '/questionresult-rest[/:id]',
                    'constraints' => [
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'QuestionRest\Controller\QuestionresultRest'
                    ],
                ],
            ],
        ],
        
    ],
    'view_manager' => [
       'template_path_stack' => [
            'question-rest' => __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
];
