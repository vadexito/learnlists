<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Model\Question;          
use Question\Form\QuestionForm; 

class ListquestController extends AbstractActionController
{
    protected $listTable;
    
    public function indexAction()
    {
        $em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        
        $rep = $em->getRepository('Question\Entity\Listquest');
        
        $lists = $rep->findAll();
        
        return [
            'lists'  => $lists
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
    
    public function getListTable()
    {
        if (!$this->listTable) {
            $sm = $this->getServiceLocator();
            $this->listTable = $sm->get('Question\Model\ListquestTable');
        }
        return $this->listTable;
    }
}

