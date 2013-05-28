<?php 

return [
    'modules' => [
        'ZendDeveloperTools',
        'SlmLocale',
        'ZF2NetteDebug',
        'ZF2PhpSettings',
        'EdpSuperluminal',
        'EdpModuleLayouts',        
        'DoctrineModule',
        'DoctrineORMModule', 
        'ZfcBase',
        'ZfcUser',
        'ZfcUserAdmin',
        'ZfcUserDoctrineORM',
        'ZfcTwitterBootstrap',
        'WebinoImageThumb',           
        'DluTwBootstrap',        
        'GoalioMailService',
        'CdliTwoStageSignup',     
        'GoalioForgotPassword',
        'GoalioForgotPasswordDoctrineORM',
        'CdliUserProfile',            
        'BjyAuthorize',
        'ZfcAdmin',        
        'WtRating', 
        'WtRatingDoctrineORM', 
        'GoalioRememberMe',
        'GoalioRememberMeDoctrineORM',
        'DelCountriesFlags',
        'PhlyContact', 
        'FileBank',
        'AsseticBundle',
        'VxoLocale',
        'SlmGoogleAnalytics',
        'ZF2FileUploadExamples',
        'Application',         
        'PhlySimplePage',
        'VxoUtils', 
        'VxoBraintree', 
        'VxoOffers', 
        'VxoDisqus', 
        'VxoReview',
        'LrnlLearn',
        'LrnlListquests',
        'LrnlUser',
        'LrnlRest',
        'LrnlHelp',
        'LrnlSearch', 
        'LrnlCategory',        
    ],
    
    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => [
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => [
            './module',
            './vendor',
        ],

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively overide configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => [
            'config/autoload/global/{,*.}{global}.php',
            'config/autoload/local/{,*.}{local}.php',
        ],

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        //'config_cache_enabled' => true,

        // The key used to create the configuration cache file name.
        'config_cache_key' => 'configcache',

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        //'module_map_cache_enabled' => true,

        // The key used to create the class map cache file name.
        'module_map_cache_key' => 'modulecache',

        // The path in which to cache merged configuration.
        'cache_dir' => './data/cache',

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
         'check_dependencies' => true,
    ],

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => [
    //     [
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Zend\ServiceManager\Config.
   // 'service_manager' => [),
];