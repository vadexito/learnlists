<?php

namespace LrnlListquests\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use LrnlListquests\Entity\Question;          
use LrnlListquests\Form\EditQuestionForm;     
use LrnlListquests\Entity\Listquest;     
use LrnlListquests\Provider\ProvidesEntityManager;

class QuestionController extends AbstractActionController
{
    use ProvidesEntityManager;
    
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
        $question = $this->getEntityManager()
                         ->find(get_class(new Question()),$id);
        
        if (!$this->_checkUserIsAuthorized($question->listquest)) {
            return $this->redirect()->toRoute('home');
        }
        
        $form  = $this->getServiceLocator()->get('edit-question-form');
        $form->bind($question);
        $form->get('submit')->setValue(_('Edit'));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($question->getInputFilter());
            $form->setData($request->getPost());
            $form->isValid(); 
            if ($form->isValid()) {
                
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute(
                    'listquests/list',
                    ['action' => 'edit','id' => $question->listquest->id]
                );
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
            return $this->redirect()->toRoute('listquests/list');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $question = $this->getEntityManager()
                         ->find(get_class(new Question()),$id);
                if ($question){
                    $listId = $question->listquest->id;
                    $this->getEntityManager()->remove($question);
                    $this->getEntityManager()->flush();
                }
            }
            return $this->redirect()->toRoute(
                'listquests/list',
                ['action' => 'show','id' => $listId]
            );
        }

        return [
            'id' => $id,
            'question' => $this->getEntityManager()
                               ->find(get_class(new Question()),$id),
        ];
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
}

