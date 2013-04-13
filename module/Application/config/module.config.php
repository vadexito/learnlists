<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => 'LrnlListquests\Controller\Listquest',
                        'action'     => 'home',
                    ],
                ],
            ],
            'footer' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/footer',
                    'defaults' => [
                        'controller' => 'PhlySimplePage\Controller\Page'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'about' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/about',
                            'defaults' => [
                                'template'   => 'application/footer/about',
                            ],
                        ],
                    ],
                    'terms' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/terms',
                            'defaults' => [
                                'template'   => 'application/footer/terms',
                            ],
                        ],
                    ],
                    'privacy' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/privacy',
                            'defaults' => [
                                'template'   => 'application/footer/privacy',
                            ],
                        ],
                    ],
                    'help' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/help',
                            'defaults' => [
                                'template'   => 'application/footer/help',
                            ],
                        ],
                    ],
                ],
            ],
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'Navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
            'wtrating.mapper' => function ($sm) {     
                return new WtRating\Mapper\DoctrineMapper(
                        $sm->get('Doctrine\ORM\EntityManager')                        
                );
            }
        ],
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Application\Controller\Index' => 'Application\Controller\IndexController',
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'phly-contact/contact/index'     => __DIR__ . '/../view/phly-contact/contact/index.phtml',
            'phly-contact/contact/thank-you' => __DIR__ . '/../view/phly-contact/contact/thank-you.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'app_zfcuser_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/xml',
            ],
            'app_zfr_forum_driver' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'cache' => 'array',
                'paths' => __DIR__ . '/xml',
            ],            
            'app_wtrating_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\XmlDriver',
                'paths' => __DIR__ . '/xml',
            ],
            'orm_default' => [
                'drivers' => [
                    'ZfcUserLL\Entity' => 'app_zfcuser_entity',
                    'WtRating\Entity' => 'app_wtrating_entity',
                ]
            ],            
        ],        
    ],
    'assetic_configuration' => [
        'routes' => [
            'home' => [
                // Is disabled because 'default' option key will mix with this configuration section
                // and provide @base_css assets.
                // '@base_css',
                '@base_js',
            ],
        ],

        'default' => [
            'assets' => [
                '@base_css',
            ],
            'options' => [
                'mixin' => true
            ],
        ],

        'modules' => [
            /*
             * Application moodule - assets configuration
             */
            'application' => [

                # module root path for yout css and js files
                'root_path' => __DIR__ . '/../assets',

                # collection od assets
                'collections' => [

                    'base_css' => [
                        'assets' => [
//                            'css/bootstrap/css/bootstrap-responsive.min.css',
//                            'css/style.css',
//                            'css/bootstrap/css/bootstrap.min.css'                            
                        ],
                        'filters' => [
                            'CssRewriteFilter' => [
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ],
                        ],
                        'options' => [],
                    ],

                    'base_js' => [
                        'assets' => [
//                            'js/lib/jquery.min.js',
//                            'js/lib/bootstrap.min.js'
                        ],
                    ],

                    'base_images' => [
                        'assets' => [
//                            'images/*.png',
//                            'images/*.ico',
//                            'images/*.jpg'
                        ],
                        'options' => [
                            'move_raw' => true,
                        ],
                    ],
                ],
            ],
        ],
    ],
];
