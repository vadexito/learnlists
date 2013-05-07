<?php

namespace LrnlHelp;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

use PhlySimplePage\PageController;

class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface
{
    public function onBootstrap($e)
    {
        $app    = $e->getTarget();
        $events = $app->getEventManager();
        $events->attach('route', array($this, 'onRoutePost'), -100);
    }
    
    public function onRoutePost($e)
    {
        $matches = $e->getRouteMatch();
        if (!$matches) {
            return;
        }

        $controller = $matches->getParam('controller');
        if ($controller != 'PhlySimplePage\Controller\Page') {
            return;
        }

        $app    = $e->getTarget();
        $events = $app->getEventManager();
        $shared = $events->getSharedManager();
        $shared->attach('PhlySimplePage\PageController', 'dispatch', array($this, 'onDispatchPost'), -1);
    }

    public function onDispatchPost($e)
    {
        $target = $e->getTarget();
        
        if (!$target instanceof PageController) {
            return;
        }
        
        $config = $e->getApplication()->getServiceManager()->get('config');
        if (isset($config['module_layouts'][__NAMESPACE__])) {
            $pluginManager = new \Zend\Mvc\Controller\PluginManager();
            $pluginManager->setController($target);
            $pluginManager->get('layout')->setTemplate($config['module_layouts'][__NAMESPACE__]);
        }
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }
    
    public function getServiceConfig()
    {
        return [
            'factories' => [
                'help_center_navigation' => 'LrnlHelp\Navigation\Service\HelpNavigationFactory',
            ],
        ];
    }
}
