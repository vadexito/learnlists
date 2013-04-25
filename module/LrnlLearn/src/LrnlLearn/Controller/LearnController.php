<?php

namespace LrnlLearn\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class LearnController extends AbstractActionController
{
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
        
        return [
            'listId'    => $id,
            'maxRound' => 5,
            'timePerQuestion' => 15
        ];
    }
}

