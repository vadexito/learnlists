<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Entity\Listquest;          
use Question\Form\ListquestForm; 
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;

class ListquestController extends AbstractActionController
{
    protected $listquestService = NULL;
    
    public function indexAction()
    {
        $ratingService = $this->getServiceLocator()->get('wtrating.service');
        
        return [
            'ratingService' => $ratingService,
            'lists' => $this->getListquestService()->fetchAll()
        ];
    }
    
    public function homeAction()
    {
        return [
            'lists' => $this->getListquestService()->fetchAll()
        ];
    }
    
    public function showAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }        
        return [
            'list'    => $this->getListquestService()->fetchById($id)
        ];
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('Question\Form\ListquestForm');        
        $form->get('submit')->setValue(_('Add'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $listquest = $this->getListquestService()->generateNewListquest();
            
            $form->bind($listquest);
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
     
                $this->getListquestService()->insertListquest($listquest);

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
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('list');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $list = $this->getListquestService()->fetchById($id);
                if ($list){
                    $this->getListquestService()->deleteListquest($list);
                }
            }
            return $this->redirect()->toRoute('list');
        }

        return [
            'id' => $id,
            'list' => $this->getListquestService()->fetchById($id),
        ];
    }
    
    
    
    public function rateAction()
    {
        $typeId = (int) $this->params()->fromRoute('id', 0);
        if (!$typeId) {
            return $this->redirect()->toRoute('home');
        }
        
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $serviceLocator = $this->getServiceLocator();
        $ratingService = $serviceLocator->get('wtrating.service');
        
        if (!$ratingService->getMapper()->hasRated($userId,$typeId)){
            $rating = $serviceLocator->create('wtrating.rating');
            $rating->setTypeId($typeId);
            $rating->setUserId($userId);
            $rating->setRating(1);
            $ratingService->persist($rating);
        }
        
        return $this->redirect()->toRoute('list');
        
    }
    
    public function getListquestService()
    {
        if ($this->listquestService === NULL){
            $this->listquestService = $this->getServiceLocator()
                        ->get('learnlists-listquestfactory-service'); 
        }
        return $this->listquestService;
    }
}

