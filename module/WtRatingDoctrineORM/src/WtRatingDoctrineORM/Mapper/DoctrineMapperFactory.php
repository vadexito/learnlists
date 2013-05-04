<?php

namespace WtRatingDoctrineORM\Mapper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DoctrineMapperFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $services)
    {
        return new DoctrineMapper(
                $services->get('Doctrine\ORM\EntityManager'),                      
                $services->get('wtrating_module_options')                      
        );
        
    }
}