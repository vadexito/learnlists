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
use Zend\Session\Container;
use Locale;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $services = $e->getApplication()->getServiceManager();
        $em = $e->getApplication()->getEventManager();
        
        //initiate translator
        $session = new Container('learnlists_locale');
        if ($session->locale){            
            $locale = $session->locale;
        } else {
            $locale = Locale::getDefault();
            $session->locale = $locale;
        }
        Locale::setDefault($locale);
        $services->get('translator')->setLocale($locale)
                                    ->setFallbackLocale('en_US');
          
        
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);
        
        //initiate default role after registering
        $entityManager = $services->get('Doctrine\ORM\EntityManager');
        $zfcuserService = $services->get('zfcuser_user_service');
        $zfcuserEventManager = $zfcuserService->getEventManager();
        $zfcuserEventManager->attach('register', function($e) use ($entityManager) {
            
            $role = $entityManager->getRepository('ZfcUserLL\Entity\Role')->find(5);
            $e->getParam('user')->addRole($role);
        });
        
        //initialize nagivation with ACL
        $authorize = $services->get('BjyAuthorize\Service\Authorize');
        $acl = $authorize->getAcl();
        $role = $authorize->getIdentity();
        \Zend\View\Helper\Navigation::setDefaultAcl($acl);
        \Zend\View\Helper\Navigation::setDefaultRole($role);
        
        //after each login save IP and date of last activity
        $zfcServiceEvents = $services->get('ZfcUser\Authentication\Adapter\AdapterChain')->getEventManager();
        $zfcServiceEvents->attach(
            'authenticate',
            function ($e) use ($entityManager) {
                $user = $e->getParams();
                $userEntity = $entityManager->getRepository('ZfcUserLL\Entity\User')
                                      ->find($user['identity']);
                $userEntity->setIp($_SERVER['REMOTE_ADDR']);
                $userEntity->setLastActivityDate(new \DateTime());
                $entityManager->flush();
            }
        );
        
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
