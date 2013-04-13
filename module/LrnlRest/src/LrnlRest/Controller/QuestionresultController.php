<?php

namespace LrnlRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use LrnlListquests\Entity\Questionresult;

class QuestionresultController extends AbstractRestfulController
{
    public function getList()
    {        
    }        
        
    public function get($id)
    {        
    }
    
    public function create($data)
    {
        $this  ->getServiceLocator()
                        ->get('learnlists-questionresultfactory-service')
                        ->createNewEntity($data);
        
        return new JsonModel((array)$questionresult->toArray());
    }
    
    public function update($id, $data)
    {
        
    }
    
    public function delete($id)
    {
        $this->getServiceLocator()
                        ->get('learnlists-questionresultfactory-service')
                        ->deleteQuestionresult($id);
        
        return new JsonModel([]);
        
    }
    
}

