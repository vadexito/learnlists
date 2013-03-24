<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Question\Provider\ProvidesEntityManager;

class AdminController extends AbstractActionController
{
    
    use ProvidesEntityManager;
    
    public function indexAction()
    {
        $rep = $this->getEntityManager()->getRepository('Question\Entity\Listquest');
        
        return [
            'lists' => $rep->findAll()
        ];
    }
}

