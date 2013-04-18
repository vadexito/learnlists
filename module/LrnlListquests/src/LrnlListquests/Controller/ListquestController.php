<?php

namespace LrnlListquests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use LrnlListquests\Entity\Listquest;

class ListquestController extends AbstractActionController
{
    protected $listquestService = NULL;
    protected $_redirectRoute = NULL;
    
    public function homeAction()
    {
        $searchForm = $this->getServiceLocator()->get('learnlists-form-search');
        return [
            'lists' => $this->getListquestService()->fetchAll(),
            'searchForm' => $searchForm
        ];
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('LrnlListquests\Form\ListquestForm');        
        $form->get('submit')->setValue(_('Add'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $listquest = $this->getListquestService()->generateNewListquest();
            
            $form->bind($listquest);
            $form->setData($request->getPost());
            
            if ($form->isValid()) {
     
                $this->getListquestService()->insertListquest($listquest);
                return $this->redirect()->toRoute($this->getRedirectRoute());
            }
        }
        return ['form' => $form];
    }

    public function editAction()
    {
        $listId = (int) $this->params()->fromRoute('id', 0);
        if (!$listId) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $listquest = $this->getListquestService()->fetchById($listId);
        
        if (!$this->_checkUserIsAuthorized($listquest)) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        
        $form = $this->getServiceLocator()->get('LrnlListquests\Form\EditQuestionsInListquestForm');
        $form->get('submit')->setValue(_('Add'));
        $form->bind($listquest);  
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $form->setData($request->getPost()); 
            if ($form->isValid()) {  
                $listquest = $this->getListquestService()->updateListquest($listquest);

                return $this->redirect()->toRoute(
                    'listquests/list/edit',
                    ['id' => $listId]
                );
            }
        }
        return ['form' => $form,'list' => $listquest];       
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
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
            return $this->redirect()->toRoute($this->getRedirectRoute());
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
            $ratingService->getMapper()->persist($rating);
            
        }
        
        return $this->redirect()->toRoute('lrnl-search');
        
    }
    
    public function getListquestService()
    {
        if ($this->listquestService === NULL){
            $this->listquestService = $this->getServiceLocator()
                        ->get('learnlists-listquestfactory-service'); 
        }
        return $this->listquestService;
    }
    
    protected function _checkUserIsAuthorized(Listquest $listquest)
    {
        $user = $this->zfcUserAuthentication()->getIdentity();
        
        foreach ($user->getRoles() as $role){
            if ($role->getRoleId() === 'admin'){
                return true;
            }
        }
        
        return $listquest && ($listquest->author->getId() === $user->getId());
    }
    
    public function setRedirectRoute($route)
    {
        $this->_redirectRoute = $route;
            
        return $this;
    }
    
    public function getRedirectRoute()            
    {
        if ($this->_redirectRoute === NULL)
        {
            $config = $this->getServiceLocator()->get('config');
            $configModule = $config['lrnl-listquests'];
            $this->_redirectRoute = $configModule['redirect_route_after_listquestCrud'];
        }
        return $this->_redirectRoute;
    }
}

