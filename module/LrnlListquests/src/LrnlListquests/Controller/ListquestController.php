<?php

namespace LrnlListquests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use LrnlListquests\Entity\Listquest;
use LrnlListquests\Provider\ProvidesListquestService;
use LrnlSearch\Provider\ProvidesSearchService;
use LrnlCategory\Provider\ProvidesCategoryService;
use VxoReview\Provider\ProvidesReviewService;
use Zend\Http\PhpEnvironment\Response;
use Zend\I18n\Translator\TranslatorAwareTrait;
use \Zend\I18n\Translator\TranslatorAwareInterface;

class ListquestController extends AbstractActionController 
    implements TranslatorAwareInterface
{
    protected $_redirectRoute = NULL;
    
    use ProvidesListquestService;
    use ProvidesSearchService;
    use ProvidesCategoryService;
    use ProvidesReviewService;
    use TranslatorAwareTrait;
    
    public function homeAction()
    {
        $searchForm = $this->getServiceLocator()->get('learnlists-form-search');
        
        return [
            'lists' => $this->getListquestService()->fetchAllSortBy('questions'),
            'categories'=> $this->getCategoryService()->fetchByDepth(2),
            'searchForm' => $searchForm,
        ];
    }
    
    public function showAction()
    {
        $listId = (int) $this->params()->fromRoute('id', 0);        
        $reviews = $this->getReviewService()->fetchByReviewedItem($listId);        
        return ['reviews' => $reviews];
    }
    
    public function addAction()
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form = $formElementManager->get('listquest-create-form');
        
        $tempFile = null;
        
        $prg = $this->fileprg($form);
        if ($prg instanceof Response) {
            return $prg; 
        } elseif (is_array($prg)) {
                
            if ($form->isValid()) {
                $listquest = $form->getData();

                $id = $this->getListquestService()->insertListquest($listquest);                
                $this->getSearchService()->updateIndex($listquest);
                $messageSuccess = $this->getTranslator()->translate('You have successfully created a new empty quiz and you can now add questions');
                $this->flashMessenger()->addSuccessMessage($messageSuccess);
                return $this->redirect()->toRoute(
                    'listquests/list/edit',
                    ['id' => $id]
                );
            } else {
                
                $fileErrors = $form->get('listquest')->get('pictureId')->getMessages();
                if (empty($fileErrors)) {
                    $tempFile = $form->get('listquest')->get('pictureId')->getValue();
                }
            }
        }
        
        return [
            'form' => $form,
            'tempFile' => $tempFile,
        ];
    }
    
    public function changepictureAction()
    {
        $listId = (int) $this->params()->fromRoute('id', 0);
        if (!$listId) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $listquest = $this->getListquestService()->fetchById($listId);
        
        if (!$listquest || !$this->_checkUserIsAuthorized($listquest)) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        $form = $formElementManager->get('listquest-changepicture-form');    
        
        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive(
                $this->getRequest()->getPost()->toArray(),
                $this->getRequest()->getFiles()->toArray()
            );

            $form->setData($data);
            $form->bind($listquest);
            if ($form->isValid()) {   
                $form->getData(); 
                
                $this->getListquestService()->updateListquest($listquest);
                $message = $this->getTranslator()->translate('Your picture has been successfully changed');
                $this->flashMessenger()->addSuccessMessage($message);
                
            } else {    
                $errorMessage = $this->getTranslator()->translate('Your picture could not be changed. Please try again.');
                $this->flashMessenger()->addErrorMessage($errorMessage);
                if (isset($form->getMessages()['listquest']['pictureId'])){
                    foreach ($form->getMessages()['listquest']['pictureId'] as $message){
                        $this->flashMessenger()->addInfoMessage($message);
                    }
                }  
            }
        }
        return $this->redirect()->toRoute(
            'listquests/list/edit',
            ['id' => $listquest->id]
        );
    }

  
    public function editAction()
    {
        $formElementManager = $this->getServiceLocator()->get('FormElementManager');
        
        $listId = (int) $this->params()->fromRoute('id', 0);
        if (!$listId) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        $tempFile = null;
        $listquest = $this->getListquestService()->fetchById($listId);
        
        if (!$listquest || !$this->_checkUserIsAuthorized($listquest)) {
            return $this->redirect()->toRoute($this->getRedirectRoute());
        }
        
        $form = $formElementManager->get('listquest-edit-form');
        $form->get('submit')->setValue(_('Save'));
        
        $form->bind($listquest);
        $request = $this->getRequest();
        if ($request->isPost()) {   
            $form->setData($request->getPost());
            if ($form->isValid()) {

                $this->getListquestService()->updateListquest($form->getData());
                $this->getSearchService()->updateIndex($form->getData());
                $message = $this->getTranslator()->translate('You have successfully updated your quiz');
                $this->flashMessenger()->addSuccessMessage($message);
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
            'changePictureForm' => $formElementManager->get('listquest-changepicture-form')
        ];   
    }

    public function deleteAction()
    {
        $translator = $this->getServiceLocator()->get('translator');
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
                $message = $this->getTranslator()->translate('You have successfully deleted your quiz');
                $this->flashMessenger()->addSuccessMessage($message);
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

