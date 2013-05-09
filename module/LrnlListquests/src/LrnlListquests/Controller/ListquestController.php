<?php

namespace LrnlListquests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use LrnlListquests\Entity\Listquest;
use LrnlListquests\Provider\ProvidesListquestService;
use LrnlSearch\Provider\ProvidesSearchService;

use LrnlListquests\InputFilter\Picture as PictureInputFilter;

class ListquestController extends AbstractActionController
{
    protected $listquestService = NULL;
    protected $_redirectRoute = NULL;
    
    use ProvidesListquestService;
    use ProvidesSearchService;
    
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
            
            $data = $request->getPost();
            //include element for file upload
            if ($this->getRequest()->getFiles()){
                
                $data = array_merge_recursive(
                    $request->getPost()->toArray(),
                    $request->getFiles()->toArray()
                );
                
                $targetUpload = $this->getServiceLocator()
                                     ->get('lrnllistquests_module_options')
                                     ->getTmpPictureUploadDir();
                $fileFilter = new PictureInputFilter('category_picture',$targetUpload);
                $form->getInputFilter()->getInputs()['listquest']->add($fileFilter);
            }
            $form->setData($data);
            
            if ($form->isValid()) { 
                $form->getData();
                
                //hydrate picture in filebank
                $hydrator = $this->getServiceLocator()->get('listquest_picture_hydrator');
                $picture = $form->get('listquest')->get('category_picture')->getValue();
                $hydrator->hydrate($picture,$listquest);
                
                $this->getListquestService()->insertListquest($listquest);                
                $this->getSearchService()->updateIndex($listquest);
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
                
                $this->getListquestService()->updateListquest($listquest);
                $this->getSearchService()->updateIndex($listquest);
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
                $this->getSearchService()->deleteFromIndex($id);  
                $this->getListquestService()->deleteListquest($id);
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
        if ($this->_listquestService === NULL){
            $sm = $this->getServiceLocator();
            $this->_listquestService = $sm->get('learnlists-listquestfactory-service'); 
        }
        return $this->_listquestService;
    }
    
    public function getSearchService()
    {
        if ($this->_searchService === NULL){
            $this->_searchService = $this->getServiceLocator()
                        ->get('learnlists-search-service-factory'); 
        }
        return $this->_searchService;
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
            $configModule = $this->getServiceLocator()->get('lrnllistquests_module_options');
            $this->_redirectRoute = $configModule->getRedirectRouteAfterListquestCrud();
        }
        return $this->_redirectRoute;
    }
}

