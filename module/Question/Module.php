<?php

namespace Question;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Question\Model\Question;
use Question\Model\QuestionTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module
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
    
    public function getServiceConfig()
    {
        return [
            'factories' => [
                'Question\Model\QuestionTable' =>  function($sm) {
                    $tableGateway = $sm->get('QuestionTableGateway');
                    $table = new QuestionTable($tableGateway);
                    return $table;
                },
                'QuestionTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Question());
                    return new TableGateway('question', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
