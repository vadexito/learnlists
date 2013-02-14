<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Model\Question;          
use Question\Form\QuestionForm;       


class QuestionController extends AbstractActionController
{
    protected $questionTable;
    
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
        
        return [
            'listId'    => $id,
            'form'      => new QuestionForm(),
        ];
    }

    public function addAction()
    {
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
    }
    
    public function getQuestionTable()
    {
        if (!$this->questionTable) {
            $sm = $this->getServiceLocator();
            $this->questionTable = $sm->get('Question\Model\QuestionTable');
        }
        return $this->questionTable;
    }
}

