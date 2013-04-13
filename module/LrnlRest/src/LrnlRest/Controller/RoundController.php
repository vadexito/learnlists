<?php

namespace LrnlRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class RoundController extends AbstractRestfulController
{
    /**
     * get all the rounds for a given member and a given list of questions
     * (listquest)
     * @return boolean|\Zend\View\Model\JsonModel
     */
    
    public function getList()
    {
        $listquestId = (int) $this->params()->fromQuery('listquestId',0);  
        $result = $this ->getServiceLocator()
                        ->get('learnlists-roundfactory-service')
                        ->fetchByUserByListquestId($listquestId);
        
        return new JsonModel($result);
    }        
    
    public function get($id)
    {     
    }
    
    public function create($data)
    {
        $round = $this  ->getServiceLocator()
                        ->get('learnlists-roundfactory-service')
                        ->createNewEntity($data);
        
        return new JsonModel((array)$round->toArray());
    }
    
    public function update($id, $data)
    {
    }
    
    public function delete($id)
    {
        $this  ->getServiceLocator()
                        ->get('learnlists-roundfactory-service')
                        ->deleteRound($id);
        
        return new JsonModel([]);
    }
}

