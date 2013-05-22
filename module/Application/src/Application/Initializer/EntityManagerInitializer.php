<?php

namespace Application\Initializer;

use Zend\ServiceManager\InitializerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

class EntityManagerInitializer implements InitializerInterface
{
    public function initialize($instance, ServiceLocatorInterface $serviceLocator)
    {
        if ($instance instanceof ObjectManagerAwareInterface) {
            
            $objectManager = $serviceLocator->getServiceLocator()
                                            ->get('Doctrine\ORM\EntityManager');                        
            $instance->setObjectManager($objectManager);
        }
    }    
}
