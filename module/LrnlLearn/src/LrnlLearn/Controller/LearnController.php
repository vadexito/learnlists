<?php

namespace LrnlLearn\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LearnController extends AbstractActionController
{
    public function indexAction()
    {
        $sl = $this->getServiceLocator();
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            if ($this->zfcUserAuthentication()->hasIdentity()){
                return $this->redirect()->toRoute('learn/summary');
            } 
            return $this->redirect()->toRoute('zfcuser/login');
        }
        
        $listquest = $sl->get('learnlists-listquestfactory-service')
                                  ->fetchById($id);
        if (!$listquest || $listquest->questions->count() === 0){
            return $this->redirect()->toRoute('learn/summary');
        }
        
        $reviewCreate = $this->forward()->dispatch(
            'VxoReview\Controller\Review',
            [
                'action' => 'create',
                'entityId' => $id
            ]
        );
        $reviewCreate->setTemplate('lrnl-learn/learn/reviewcreate.phtml');
        
        return [
            'listId'    => $id,
            'timePerQuestion' => 15,
            'reviewCreate' => $reviewCreate
        ];
    }
    
    public function summaryAction()
    {
        return [];
    }
}

