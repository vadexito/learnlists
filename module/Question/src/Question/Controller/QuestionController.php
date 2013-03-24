<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Entity\Question;          
use Question\Form\QuestionForm;     
use Question\Entity\Listquest;     
use Question\Provider\ProvidesEntityManager;

class QuestionController extends AbstractActionController
{
    use ProvidesEntityManager;
    
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
        
        return [
            'listId'    => $id,
            'maxRound' => 5
        ];
    }

    public function addAction()
    {
        $listId = (int) $this->params()->fromRoute('id', 0);
        if (!$listId) {
            return $this->redirect()->toRoute('home');
        }
        
        $listquest = $this  ->getEntityManager()
                            ->find('Question\Entity\Listquest',$listId);
        if (!$this->_checkUserIsAuthorized($listquest)) {
            return $this->redirect()->toRoute('home');
        }
        
        $form = new QuestionForm();
        $form->get('listId')->setValue($listId);
        $form->get('submit')->setValue(_('Add'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $question = new Question();
            $form->setInputFilter($question->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {                
                $question->exchangeArray($form->getData(),$this->getEntityManager());
                $this->getEntityManager()->persist($question);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute(
                    'list/show',
                    ['id' => $listId]
                );
            }
        }
        return ['form' => $form,'listId' => $listId];        
        
    }

    public function editAction()
    {
        
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', [
                'action' => 'add'
            ]);
        }
        $question = $this->getEntityManager()
                         ->find('Question\Entity\Question',$id);
        
        $form  = new QuestionForm();
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
                    'list',
                    ['action' => 'show','id' => $question->listquest->id]
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
            return $this->redirect()->toRoute('list');
        }
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $question = $this->getEntityManager()
                         ->find('Question\Entity\Question',$id);
                if ($question){
                    $listId = $question->listquest->id;
                    $this->getEntityManager()->remove($question);
                    $this->getEntityManager()->flush();
                }
            }
            return $this->redirect()->toRoute(
                'list',
                ['action' => 'show','id' => $listId]
            );
        }

        return [
            'id' => $id,
            'question' => $this->getEntityManager()
                               ->find('Question\Entity\Question',$id),
        ];
    }
    
    
    protected function _checkUserIsAuthorized(Listquest $listquest)
    {
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        return $listquest && ($listquest->author->getId() === $userId);
    }
}

