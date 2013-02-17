<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Entity\Listquest;          
use Question\Form\ListquestForm; 

class ListquestController extends AbstractActionController
{
    protected $listTable;
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
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
        $form = new ListquestForm();
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $listquest = new Listquest(
                $this->getEntityManager(),
                $this->zfcUserAuthentication()->getIdentity()
            );
            $form->setInputFilter($listquest->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $listquest->exchangeArray($form->getData());
                $this->getEntityManager()->persist($listquest);
                $this->getEntityManager()->flush();

                // Redirect to list of questions
                return $this->redirect()->toRoute('list');
            }
        }
        return ['form' => $form];   
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
    }
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }
 
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    } 
}

