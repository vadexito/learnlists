<?php

namespace WtRatingDoctrineORM;

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceConfigProviderInterface;

class Module implements AutoloaderProviderInterface,
       ConfigProviderInterface,
       ServiceConfigProviderInterface
{
    public function onBootstrap($e)
    {
        $app     = $e->getParam('application');
        $sm      = $app->getServiceManager();
        $options = $sm->get('wtrating_module_options');

        // Add the default entity driver only if specified in configuration
        if ($options->getEnableDefaultEntities()) {
            $chain = $sm->get('doctrine.driver.orm_default');
            $chain->addDriver(new XmlDriver(__DIR__ . '/config/xml/wtrating'), 'WtRating\Entity');
        }
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getServiceConfig()
    {
        return [
            'factories' => [
                'wtrating_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['wtrating']) ? $config['wtrating'] : []);
                },
                'wtrating.mapper' => 'WtRatingDoctrineORM\Mapper\DoctrineMapperFactory', 
            ],
        ];
    }

    
}
