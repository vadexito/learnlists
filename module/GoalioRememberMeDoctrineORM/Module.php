<?php

namespace GoalioRememberMeDoctrineORM;

use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\EventManager\EventInterface;

class Module implements
    AutoloaderProviderInterface,
    ConfigProviderInterface,
    ServiceProviderInterface,
    BootstrapListenerInterface
 
{
    public function onBootstrap(EventInterface $e)
    {
        $app     = $e->getParam('application');
        $sm      = $app->getServiceManager();
        $options = $sm->get('goaliorememberme_module_options');

        // Add the default entity driver only if specified in configuration
        if ($options->getEnableDefaultEntities()) {
            $chain = $sm->get('doctrine.driver.orm_default');
            $chain->addDriver(new XmlDriver(__DIR__ . '/config/xml/goalioremembermedoctrineorm'), 'GoalioRememberMeDoctrineORM\Entity');
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

    public function getServiceConfig()
    {
        
        $config= array(
            'aliases' => array(
                'goaliorememberme_doctrine_em' => 'doctrine.entitymanager.orm_default',

            ),
            'factories' => array(
                'goaliorememberme_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['goaliorememberme']) ? $config['goaliorememberme'] : array());
                },
                'goaliorememberme_rememberme_mapper' => function ($sm) {
                    return new \GoalioRememberMeDoctrineORM\Mapper\RememberMe(
                        $sm->get('goaliorememberme_doctrine_em'),
                        $sm->get('goaliorememberme_module_options')
                    );
                },
            ),
        );
                
        return $config;
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
