<?php

namespace LrnlUser;


use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Form\Element\Text;

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
        
        //get user entity class
        $zfcuserOptions = $services->get('zfcuser_module_options');
        $userEntityClass = $zfcuserOptions->getUserEntityClass();
        
        //get role entity class
        $config = $services->get('BjyAuthorize\Config');
        if ( ! isset($config['role_providers']['BjyAuthorize\Provider\Role\ObjectRepositoryProvider'])) {
            throw new InvalidArgumentException(
                'Config for "BjyAuthorize\Provider\Role\ObjectRepositoryProvider" not set'
            );
        }
        $providerConfig = $config['role_providers']['BjyAuthorize\Provider\Role\ObjectRepositoryProvider'];
        if ( ! isset($providerConfig['role_entity_class'])) {
            throw new InvalidArgumentException('role_entity_class not set in the bjyauthorize role_providers config.');
        }
        $roleEntityClass = $providerConfig['role_entity_class'];
        
        $em = $e->getApplication()->getEventManager();
        $entityManager = $services->get('Doctrine\ORM\EntityManager');
        
        //add element to register form
        $events = $em->getSharedManager();
        $events->attach('ZfcUser\Form\Register','init', function($e) use ($entityManager,$roleEntityClass) {
            $form = $e->getTarget();
            $roleElement = new Form\Element\Role('role',$entityManager,$roleEntityClass);
            $form->add($roleElement);
        });
        
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($em);
        
        //initiate user profile after registering
        $zfcuserService = $services->get('zfcuser_user_service');
        $zfcuserEventManager = $zfcuserService->getEventManager();
        $hydrator = new DoctrineHydrator($entityManager, $userEntityClass);
        $zfcuserEventManager->attach('register', function($e) use ($hydrator) {
            
            $form = $e->getParam('form');
            $user = $e->getParam('user');
            
            $hydrator->hydrate([
                'roles' => [$form->get('role')->getValue()],
                'creationDate' => new DateTime(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'lastActivityDate' => new DateTime(),
            ], $user);
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
            function ($e) use ($entityManager,$userEntityClass) {
                $user = $e->getParam('user');
                //if authentical successful
                if ($e->getParams('code') === Result::SUCCESS){
                    $userEntity = $entityManager->getRepository($userEntityClass)
                                      ->find($user['identity']);
                    $userEntity->setIp($_SERVER['REMOTE_ADDR']);
                    $userEntity->setLastActivityDate(new DateTime());
                    $entityManager->flush();
                }
            }
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'CdliUserProfile\Form\Section\ZfcUser' => function($sm) {
                    $obj = new \CdliUserProfile\Form\Section\ZfcUser($sm->get('zfcuser_module_options'));
                    $obj->setInputFilter($sm->get('CdliUserProfile\Form\Section\ZfcUserFilter'));
                    $obj->setHydrator($sm->get('zfcuser_user_hydrator'));
                    
                    $obj->add(new Form\Element\FullName());
                    $obj->add(new Form\Element\Address());
                    
                    return $obj;
                },
                'CdliUserProfile\Form\Section\ZfcUserFilter' => function($sm) {
                    return new \CdliUserProfile\Form\Section\ZfcUserFilter(
                        $sm->get('cdliuserprofile_uemail_validator'),
                        $sm->get('cdliuserprofile_uusername_validator')
                    );
                },
            ),
        );      
    }
}
