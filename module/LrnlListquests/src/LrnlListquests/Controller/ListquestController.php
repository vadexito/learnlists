<?php

namespace LrnlListquests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use LrnlListquests\Entity\Listquest;
use LrnlListquests\Provider\ProvidesListquestService;
use LrnlSearch\Provider\ProvidesSearchService;
use LrnlListquests\Provider\ProvidesCategoryService;
use Zend\Http\PhpEnvironment\Response;


class ListquestController extends AbstractActionController
{
    protected $_redirectRoute = NULL;
    
    use ProvidesListquestService;
    use ProvidesSearchService;
    use ProvidesCategoryService;
    
    public function homeAction()
    {
        $searchForm = $this->getServiceLocator()->get('learnlists-form-search');
        
        return [
            'lists' => $this->getListquestService()->fetchAllSortBy('questions'),
            'categories'=> $this->getCategoryService()->fetchAll(),
            'searchForm' => $searchForm
        ];
    }
    
    public function addAction()
    {
        $form = $this->getServiceLocator()->get('listquest-form-create');        
        $form->get('submit')->setValue(_('Add'));
        $tempFile = null;
        
        $prg = $this->fileprg($form);
        if ($prg instanceof Response) {
            return $prg; 
        } elseif (is_array($prg)) {
            if ($form->isValid()) {
                $listquest = $form->getData();//do the work for the file
                
                if (isset($prg['picture'])){
                    $form->get('picture')->getHydrator()->hydrate($prg['picture'],$listquest);
                }

                $this->getListquestService()->insertListquest($listquest);                
                $this->getSearchService()->updateIndex($listquest);
                return $this->redirect()->toRoute($this->getRedirectRoute());
            } else {
                $fileErrors = $form->get('picture')->get('pictureId')->getMessages();
                if (empty($fileErrors)) {
                    $tempFile = $form->get('picture')->get('pictureId')->getValue();
                }
            }
        }
        
        return [
            'form' => $form,
            'tempFile' => $tempFile,
        ];
    }

    public function editAction()
    {
        $listId = (int) $this->params()->fromRoute('id', 0);
        if (!$listId) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $tempFile = NULL;
        $listquest = $this->getListquestService()->fetchById($listId);
        
        if (!$this->_checkUserIsAuthorized($listquest)) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $form = $this->getServiceLocator()->get('listquest-form-edit');
        $form->get('submit')->setValue(_('Save'));
        $prg = $this->fileprg($form);
        if ($prg instanceof Response) {
            return $prg; 
        } elseif (is_array($prg)) {
            if ($form->isValid()) {
                $listquest = $form->getData();
                if (isset($prg['picture'])){
                    $form->get('picture')->getHydrator()->hydrate($prg['picture'],$listquest);
                }
                $this->getListquestService()->updateListquest($listquest);
                $this->getSearchService()->updateIndex($listquest);
                return $this->redirect()->toRoute(
                    'listquests/list/edit',
                    ['id' => $listId]
                );
            }
        }
        return [
            'form' => $form,
            'listquest' => $listquest,
            'tempFile' => $tempFile,
        ];   
    }
//    public function editAction()
//    {
//        $listId = (int) $this->params()->fromRoute('id', 0);
//        if (!$listId) {
//            return $this->redirect()->toRoute($this->getRedirectRoute());
//        }
//        $tempFile = NULL;
//        $listquest = $this->getListquestService()->fetchById($listId);
//        
//        if (!$this->_checkUserIsAuthorized($listquest)) {
//            return $this->redirect()->toRoute($this->getRedirectRoute());
//        }
//        
//        $form = $this->getServiceLocator()->get('listquest-form-edit');
//        $form->get('submit')->setValue(_('Save'));
//        $form->bind($listquest);
//        $request = $this->getRequest(); 
//        
//        if ($request->isPost()) {   
//            $form->setData($request->getPost());
//            if ($form->isValid()) {
//                $this->getListquestService()->updateListquest($form->getData());
//                $this->getSearchService()->updateIndex($form->getData());
//                return $this->redirect()->toRoute(
//                    'listquests/list/edit',
//                    ['id' => $listId]
//                );
//            }
//        }
//        return [
//            'form' => $form,
//            'list' => $listquest,
//            'tempFile' => $tempFile,
//        ];   
//    }

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
    
    public function mapAction()
    {
        return [
            'lists' => $this->getListquestService()->fetchAll()
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

