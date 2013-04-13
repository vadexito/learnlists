<?php

namespace LrnlListquests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use LrnlListquests\Provider\ProvidesEntityManager;


class AdminController extends AbstractActionController
{
    
    use ProvidesEntityManager;
    
    public function indexAction()
    {
        
        $rep = $this->getEntityManager()->getRepository('LrnlListquests\Entity\Listquest');
        $lists = $rep->findAll();
        
        return [
            'lists' => $lists
        ];
    }
}

