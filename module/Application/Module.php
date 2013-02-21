<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $services = $e->getApplication()->getServiceManager();
        $em = $e->getApplication()->getEventManager();
        
        //initiate translator
        $services->get('translator');
        $translatorEventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($translatorEventManager);
        
        //initiate default role after registering
        $zfcuserService = $services->get('zfcuser_user_service');
        $zfcuserEventManager = $zfcuserService->getEventManager();
        $zfcuserEventManager->attach('register', function($e) use ($services) {
            
            $entityManager = $services->get('Doctrine\ORM\EntityManager');
            $role = $entityManager->getRepository('ZfcUserLL\Entity\Role')->find(5);
            $e->getParam('user')->addRole($role);
        });
        
        //initialize nagivation with ACL
        $authorize = $services->get('BjyAuthorize\Service\Authorize');
        $acl = $authorize->getAcl();
        $role = $authorize->getIdentity();
        \Zend\View\Helper\Navigation::setDefaultAcl($acl);
        \Zend\View\Helper\Navigation::setDefaultRole($role);
    }

    
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                    'WtRating' => __DIR__ . '/src/WtRating',
                    'ZfcUserLL' => __DIR__ . '/src/ZfcUserLL',
                ),
            ),
        );
    }
}
