<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'bjyauthorize' => [
        'guards'                => [
            'BjyAuthorize\Guard\Route' => [
                ['route' => 'home', 'roles' => ['guest', 'user']],                
                ['route' => 'footer/about', 'roles' => ['guest', 'user']],
                ['route' => 'footer/privacy', 'roles' => ['guest', 'user']],
                ['route' => 'footer/terms', 'roles' => ['guest', 'user']],
                ['route' => 'footer/help', 'roles' => ['guest', 'user']],
            ],
        ],
    ],
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
            'zfc-user/user/login' => __DIR__ . '/../view/zfc-user/user/login.phtml',
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
        /**
        * Module is not enabled if an MvcEvent::EVENT_DISPATCH_ERROR event occurs.
        * However, we may still want our assets for pages with dispatch errors.
        * So, we can build up a whitelist of errors for the module to be enabled for.
        */
        'acceptableErrors' => [
            //defaults
            \Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND,
            \Zend\Mvc\Application::ERROR_CONTROLLER_INVALID,
            \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH,

            //allow assets when authorisation fails when using the BjyAuthorize module
            \BjyAuthorize\Guard\Route::ERROR,
        ],
        /*
        * Enable cache
        */
       'cacheEnabled' => false,

       /*
        * Cache dir
        */
       'cachePath' => __DIR__ . '/../../../data/cache',

       /*
        * Debug on (used via \Assetic\Factory\AssetFactory::setDebug)
        *
        * @optional
        */
        'debug' => true,

       /*
        * set Umask
        * 
        * @optional
        */
        'umask' => null,

       /*
        * Define base URL which will prepend your resources address
        *
        * @example
        * <link href="http://resources.example.com/twitter_bootstrap_css.css?1320257242" media="screen" rel="stylesheet" type="text/css">
        *
        * @optional
        * @default autodetect by ZF2
        */
        'baseUrl' => null,

        'routes' => [
            'home' => [
                '@global_js'
            ],
            'lrnl-search' => [
                '@slider_css',
                '@slider_js',
                '@global_js'
            ],
            'learn/basic' => [
                '@marionette_js',
                '@jqknob_js',
                '@mvclearn_js',
                '@global_js',
                '@flippy_js'
            ],
            'listquests/list/edit' => [
                '@global_js'
            ],
        ],

        'default' => [
            'assets' => [
                '@base_js',
                '@base_css',
                '@bootstrap_js'
            ],
            'options' => [
                'mixin' => true
            ],
        ],

        'modules' => [
            'application' => [
                'root_path' => __DIR__ . '/../assets',
                'collections' => [
                    'base_css' => [
                        'assets' => [
                            'components/bootstrap/docs/assets/css/bootstrap.css',
                            'components/bootstrap/docs/assets/css/bootstrap-responsive.css',
                            'application/css/style.css',        
                        ],
                        'filters' => [
                            '?CssRewriteFilter' => [
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ],
                        ],
                        'options' => [],
                    ],
                    'base_js' => [
                        'assets' => [
                            'components/jquery/jquery.min.js',
                            'components/html5shiv/dist/html5shiv.js',
                            'components/spin.js/dist/spin.min.js',
                        ],
                    ],
                    'bootstrap_js' => [
                        'assets' => [
                            'components/bootstrap/docs/assets/js/bootstrap-tooltip.js',
                            'components/bootstrap/docs/assets/js/bootstrap-popover.js',
                            'components/bootstrap/docs/assets/js/bootstrap-modal.js',
                            'components/bootstrap/docs/assets/js/bootstrap-collapse.js',
                            'components/bootstrap/docs/assets/js/bootstrap-dropdown.js',
                            'components/bootstrap/docs/assets/js/bootstrap-button.js',
                        ],
                    ],
                    'global_js' => [
                        'assets' => [
                            'application/js/global.js',
                        ],
                    ],
                    'slider_js' => [
                        'assets' => [
                            'application/js/lib/bootstrap-slider.js',
                        ],
                    ],
                    'slider_css' => [
                        'assets' => [
                            'application/css/vendor/slider.css',
                        ],
                    ],
                    'mvclearn_js' => [
                        'assets' => [
                            'application/js/backboneMvc/Model/*.js',
                            'application/js/backboneMvc/View/*.js',
                            'application/js/backboneMvc/learnMVC.js',
                        ],
                    ],
                    'jqknob_js' => [
                        'assets' => [
                            'components/jquery-knob/js/jquery.knob.js',
                        ],
                    ],
                    'marionette_js' => [
                        'assets' => [
                            'components/underscore/underscore-min.js',
                            'components/backbone/backbone-min.js',
                            'components/marionette/lib/backbone.marionette.min.js',
                        ],
                    ],
                    'flippy_js' => [
                        'assets' => [
                            'application/js/lib/jquery.flippy.min.js',
                        ],
                    ],
                    'base_images' => [
                        'assets' => [
                            'images/*.png',
                            'images/icons/*.png',
                            'images/thumbnails/categories/*.jpg',
                            'images/*.png',
                            'images/*.ico',
                            'images/*.jpg'
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
