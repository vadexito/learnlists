<?php

namespace LrnlLearn\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LearnController extends AbstractActionController
{
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            if ($this->zfcUserAuthentication()->hasIdentity()){
                return $this->redirect()->toRoute('learn/summary');
            } 
            return $this->redirect()->toRoute('zfcuser/login');
        }
        
        $listquest = $this->getServiceLocator()->get('learnlists-listquestfactory-service')
                                  ->fetchById($id);
        if (!$listquest || $listquest->questions->count() === 0){
            return $this->redirect()->toRoute('learn/summary');
        }
        
        return [
            'listId'    => $id,
            'timePerQuestion' => 15
        ];
    }
    
    public function summaryAction()
    {
        return [];
    }
}

