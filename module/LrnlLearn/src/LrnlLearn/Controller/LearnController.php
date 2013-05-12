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
            } else {
                return $this->redirect()->toRoute('zfcuser/login');
            }
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

