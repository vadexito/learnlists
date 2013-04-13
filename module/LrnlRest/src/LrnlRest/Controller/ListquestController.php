<?php

namespace LrnlRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ListquestController extends AbstractRestfulController
{
    public function getList()
    {
    }        
        
    public function get($id)
    {
        $list = $this   ->getServiceLocator()
                        ->get('learnlists-listquestfactory-service')
                        ->fetchById($id);
                
        return new JsonModel($list->toArray());
    }
    
    public function create($data)
    {        
    }
    
    public function update($id, $data)
    {
    }
    
    public function delete($id)
    {
    }
}

