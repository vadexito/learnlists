<?php

namespace LrnlUser;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use DateTime;
use Zend\View\Helper\Navigation;
use Zend\Authentication\Result;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class Module implements 
    AutoloaderProviderInterface,
    ConfigProviderInterface
{
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
    
    public function onBootstrap($e)
    {
        $services = $e->getApplication()->getServiceManager();
        $em = $e->getApplication()->getEventManager();
        $entityManager = $services->get('Doctrine\ORM\EntityManager');


        //add element to register form
        $events = $em->getSharedManager();
        $events->attach('ZfcUser\Form\Register','init', function($e) use ($entityManager) {
            $form = $e->getTarget();
            $form->add(new Form\RoleElement('role',$entityManager));
        });
        
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);
        
        //initiate user profile after registering
        $zfcuserService = $services->get('zfcuser_user_service');
        $zfcuserEventManager = $zfcuserService->getEventManager();
        $hydrator = new DoctrineHydrator($entityManager, 'ZfcUserLL\Entity\User');
        $zfcuserEventManager->attach('register', function($e) use ($hydrator) {
            
            $form = $e->getParam('form');
            $user = $e->getParam('user');
            
            $hydrator->hydrate([
                'roles' => [$form->get('role')->getValue()],
                'creationDate' => new DateTime(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'lastActivityDate' => new DateTime(),
            ], $user);
            
            //$role = $entityManager->getRepository('ZfcUserLL\Entity\Role')->find($form->get('role')->getValue());
            //$user->addRole($role);
//            $user->setCreationDate();
//            $user->setIp();
//            $user->setLastActivityDate();
        });
        
        //initialize nagivation with ACL
        $authorize = $services->get('BjyAuthorize\Service\Authorize');
        $acl = $authorize->getAcl();
        $role = $authorize->getIdentity();
        Navigation::setDefaultAcl($acl);
        Navigation::setDefaultRole($role);
        
        //after each login save IP and date of last activity
        $zfcServiceEvents = $services->get('ZfcUser\Authentication\Adapter\AdapterChain')->getEventManager();
        $zfcServiceEvents->attach(
            'authenticate',
            function ($e) use ($entityManager) {
                $user = $e->getParam('user');
                //if authentical successful
                if ($e->getParams('code') === Result::SUCCESS){
                    $userEntity = $entityManager->getRepository('ZfcUserLL\Entity\User')
                                      ->find($user['identity']);
                    $userEntity->setIp($_SERVER['REMOTE_ADDR']);
                    $userEntity->setLastActivityDate(new DateTime());
                    $entityManager->flush();
                }
            }
        );
    }
}
