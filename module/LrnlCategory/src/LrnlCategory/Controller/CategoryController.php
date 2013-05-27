<?php

namespace LrnlCategory\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use LrnlCategory\Provider\ProvidesCategoryService;


class CategoryController extends AbstractActionController
{
    protected $_redirectRoute = null;
    
    use ProvidesCategoryService;
    
    public function indexAction()
    {
        return [
            'categories' => $this->getCategoryService()->fetchAll()
        ];
        
    }
    public function createAction()
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');        
        $form = $formElementManager->get('category-create-form');        
        $request = $this->getRequest();
        if ($request->isPost()) { 
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getCategoryService()->insert($form->getData());
                return $this->redirect()->toRoute($this->getRedirectRoute());
            }
        }
        return [
            'form' => $form,
        ];
    }
    
    public function changepictureAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $category = $this->getCategoryService()->fetchById($id); 
        if (!$category){
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form = $formElementManager->get('category-changepicture-form');        
        
        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data); 
            $form->bind($category);
            if ($form->isValid()) {
                $form->getData();                
                $this->getCategoryService()->update($category);
                $this->flashMessenger()->addSuccessMessage(_('The picture has been successfully changed'));
                return $this->redirect()->toRoute($this->getRedirectRoute());
                
            } else {
                $this->flashMessenger()->addErrorMessage(
                    _('The picture could not be changed. Please try again.')
                );
                
                if (isset($form->getMessages()['picture']['pictureId'])){
                    foreach ($form->getMessages()['picture']['pictureId'] as $message){
                        $this->flashMessenger()->addInfoMessage($message);
                    }
                }  
            }
        }
        return [
            'id' => $id,
            'categoryName' => $category->getName(),
            'form' => $form,
        ];
        
    }
  
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $category = $this->getCategoryService()->fetchById($id);
        if (!$category){
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form = $formElementManager->get('category-edit-form');        
        $form->get('submit')->setValue(_('Save'));
        $form->bind($category);
        $request = $this->getRequest();
        if ($request->isPost()) {            
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getCategoryService()->update($form->getData());
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
                $this->getCategoryService()->delete($id);
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

