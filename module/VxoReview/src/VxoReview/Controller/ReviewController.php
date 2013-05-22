<?php

namespace VxoReview\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use VxoReview\Provider\ProvidesReviewService;


class ReviewController extends AbstractActionController
{
    protected $_redirectRoute = null;
    
    use ProvidesReviewService;
    
    public function createAction()
    {
        $entityId = (int) $this->params()->fromRoute('entityId', 0);
        if (!$entityId) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form = $formElementManager->get('review-create-form');        
        $form->get('review')->get('reviewedItem')->setValue($entityId);        
        $request = $this->getRequest();
        if ($request->isPost()) { 
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getReviewService()->insert($form->getData());
                return $this->redirect()->toRoute($this->getRedirectRoute());
            }
        }
        return [
            'entityId' => $entityId,
            'form' => $form,
        ];
    }
  
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $review = $this->getReviewService()->fetchById($id);
        if (!$review){
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form = $formElementManager->get('review-edit-form');        
        $form->get('submit')->setValue(_('Save'));
        $form->bind($review);
        $request = $this->getRequest();
        if ($request->isPost()) {            
            $form->setData($request->getPost());
            $form->isValid();
            if ($form->isValid()) {
                $this->getReviewService()->update($form->getData());
                return $this->redirect()->toRoute($this->getRedirectRoute());
            }
        }

        return [
            'id' => $id,
            'form' => $form,
        ];
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
                $this->getReviewService()->delete($id);
            }
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }

        return [
            'id' => $id
        ];
    }
    
    public function setRedirectRoute($route)
    {
        $this->_redirectRoute = $route;
            
        return $this;
    }
    
    public function getRedirectRoute()            
    {
        if ($this->_redirectRoute === null)
        {
            $this->_redirectRoute = 'home';
        }
        return $this->_redirectRoute;
    }
}

