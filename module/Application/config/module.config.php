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
                ['route' => 'staticpages/template', 'roles' => ['guest', 'user']],
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
            'staticpages' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/pages',
                    'defaults' => [
                        'controller' => 'PhlySimplePage\Controller\Page'
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'template' => [
                        'type'    => 'segment',
                        'options' => [
                            'route'    => '/:template'
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
        ],
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
                'text_domain' => 'default'
            ],
            [
                'type'        => 'phpArray',
                'base_dir'    => __DIR__ . '/../language/zend_resources',
                'pattern'     => '%s/Zend_Validate.php',
                'text_domain' => 'default',
            ],
            [
                'type'        => 'phpArray',
                'base_dir'    => __DIR__ . '/../language/zend_resources',
                'pattern'     => '%s/Zend_Captcha.php',
                'text_domain' => 'default',
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
            'layout/help'           => __DIR__ . '/../view/layout/help.phtml',
            'layout/header'           => __DIR__ . '/../view/layout/header.phtml',
            'layout/footer'           => __DIR__ . '/../view/layout/footer.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/403'               => __DIR__ . '/../view/error/403.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            'phly-contact/contact/index'     => __DIR__ . '/../view/phly-contact/contact/index.phtml',
            'phly-contact/contact/thank-you' => __DIR__ . '/../view/phly-contact/contact/thank-you.phtml',
            'zfc-user/user/login' => __DIR__ . '/../view/zfc-user/user/login.phtml',
            'about' => __DIR__ . '/../view/application/static-pages/about.phtml',
            'terms_imprint' => __DIR__ . '/../view/application/static-pages/terms_imprint.phtml',
            'how_it_works_learner' => __DIR__ . '/../view/application/static-pages/how_it_works_learner.phtml',
            'how_it_works_teacher' => __DIR__ . '/../view/application/static-pages/how_it_works_teacher.phtml',            
            'features' => __DIR__ . '/../view/application/static-pages/features.phtml',
            'zfc-user/user/login' => __DIR__ . '/../view/zfc-user/user/login.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'strToUrl' => 'Application\View\Helper\StrToUrl',
            'thumbnail' => 'Application\View\Helper\Thumbnail',
            'site' => 'Application\View\Helper\Site',
        ],
    ],
    'assetic_configuration' => [
        /**
        * Set to true if you're working in a development environment and you want for
        * every assets to be moved to public directory after some changes.
        * Set to false on production environment - to boost your application.
        * Default true - for backward compatibility.
        */
       'buildOnRequest'     => true,
        
        
        /**
         * Map how given view renderer instance will be interpreted by AsseticBundle.
         * Those are default options.
         */
        'rendererToStrategy' => array(
            'Zend\View\Renderer\PhpRenderer'  => 'AsseticBundle\View\ViewHelperStrategy',
            'Zend\View\Renderer\FeedRenderer' => 'AsseticBundle\View\NoneStrategy',
            'Zend\View\Renderer\JsonRenderer' => 'AsseticBundle\View\NoneStrategy',
        ),

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
                '@backstretch_js',
                '@global_js'
                
            ],
            'lrnl-search' => [
                '@slider_css',
                '@slider_js',
                '@global_js',
            ],
            'listquests/list/edit' => [
                '@global_js'
            ],
            'listquests/list/add' => [
                '@global_js',
//                '@jqfileupload_js',
//                '@jqfileupload_css',
            ],
        ],

        'default' => [
            'assets' => [
                '@base_js',
                '@base_css',
                '@chosen_js',
                '@chosen_css',
                '@jquery_form_js',
                
            ],
            'options' => [
                'mixin' => true
            ],
        ],

        'modules' => [
            'application' => [
                'root_path' => __DIR__ . '/../assets',
                'collections' => [                 
                    'bootstrap_less' => [
                        'assets' => [
                            'application/less/bootstrap.less',
                            'application/less/responsive.less',
                        ],
                        'filters' => [
                            'LessphpFilter' => [
                                'name' => 'Assetic\Filter\LessphpFilter'
                            ],
                        ],
                        'options' => [
                            'output' => 'bootstrap.css'
                        ],
                    ],
                    'fontawesome_css' => [
                        'assets' => [
                            'components/font-awesome/css/font-awesome.min.css',
                                   
                        ],
                        'filters' => [
                            '?CssRewriteFilter' => [
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ],
                        ],
                        'options' => [],
                    ],
                    'global_app_less' => [
                        'assets' => [
                            'application/less/style.less',  
                        ],
                        'filters' => [
                            'LessphpFilter' => [
                                'name' => 'Assetic\Filter\LessphpFilter'
                            ],
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
                            'components/bootstrap/docs/assets/js/bootstrap-transition.js',
                            'components/bootstrap/docs/assets/js/bootstrap-tab.js',
                        ],
                    ],
                    'chosen_js' => [
                        'assets' => [
                            'components/chosen/chosen/chosen.jquery.min.js',
                        ],
                    ],
                    'chosen_css' => [
                        'assets' => [
                            'components/chosen/chosen/chosen.css',
                        ],
                        'filters' => [
                            'CssRewriteFilter' => [
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ],
                        ],
                    ],
                    'jquery_js' => [
                        'assets' => [
                            'components/jquery/jquery.min.js',
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
                    'introjs_js' => [
                        'assets' => [
                            'components/intro.js/minified/intro.min.js',
                        ],
                    ],
                    'introjs_css' => [
                        'assets' => [
                            'components/intro.js/minified/introjs.min.css',
                        ],
                    ],  
                    'countdown_js' => [
                        'assets' => [
                            'components/countdown/jquery.countdown.js',
                        ],
                    ], 
                    'raty_js' => [
                        'assets' => [
                            'components/raty/lib/jquery.raty.min.js',
                        ],
                    ], 
                    'moment_js' => [
                        'assets' => [
                            'components/moment/min/moment.min.js',
                        ],
                    ],                     
                    'backstretch_js' => [
                        'assets' => [
                            'components/jquery-backstretch/jquery.backstretch.min.js',
                        ],
                    ],                     
                    'jquery_form_js' => [
                        'assets' => [
                            'components/jquery-form/jquery.form.js',
                        ],
                    ],                     
                    'animate_css' => [
                        'assets' => [
                            'components/animate.css/animate.min.css',
                        ],
                    ],
                    'fontawesome_font' => [
                        'assets' => [
                            'components/font-awesome/font/*',
                        ],
                        'options' => [
                            'move_raw' => true,
                        ],
                    ],
                    
                    'base_css' => [
                        'assets' => [
                            '@bootstrap_less',
                            '@fontawesome_css',
                            '@global_app_less'
                        ],
                        'filters' => [
                            'CssRewriteFilter' => [
                                'name' => 'Assetic\Filter\CssRewriteFilter'
                            ],
                        ],
                        'options' => [
                            'output' => 'base_css.css'
                        ],
                    ],
                    
                    'base_js' => [
                        'assets' => [
                            '@jquery_js',
                            'components/html5shiv/dist/html5shiv.js',
                            'components/spin.js/dist/spin.min.js',
                            '@bootstrap_js'
                        ],
                        'options' => [
                            'output' => 'base_js.js'
                        ],
                    ],
                    'bootstrap_img' => [
                        'assets' => [
                            'components/bootstrap/img/*',
                        ],
                        'options' => [
                            'move_raw' => true,
                        ],
                    ],
                    'chosen_img' => [
                        'assets' => [
                            'components/chosen/chosen/*.png',
                        ],
                        'options' => [
                            'move_raw' => true,
                        ],
                    ],
                    'base_images' => [
                        'assets' => [
                            'application/images/icons/*',
                            'application/images/logo/*',
                            'images/*.png',
                            'images/icons/*.png',
                            'images/homeslides/*.png',
                            'images/homeslides/*.jpg',
                            'images/adds/*',
                            'images/thumbnails/categories/*.jpg',
                            'images/*.png',
                            'images/*.ico',
                            'images/*.jpg',
                            'components/raty/lib/img/*'
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
