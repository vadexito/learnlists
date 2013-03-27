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
        
        $form = $this->getServiceLocator()->get('Question\Form\EditQuestionsInListquestForm');
        $form->get('submit')->setValue(_('Add'));
        $hydratorList = $form->getHydrator();
        $hydratorQuest = $form->get('listquest')->get('questions')->getHydrator();
        
        
        
        
//        $data = $this->getRequest()->getPost()->toArray();
//        var_dump($data);
//        $hydratorList->hydrate($data,$listquest);
//        
//        \Doctrine\Common\Util\Debug::dump($listquest->questions);die;
        
        
        
        $form->bind($listquest);  
        $request = $this->getRequest();
        if ($request->isPost()) {  
            
            $form->setData($request->getPost()); 
            var_dump($request->getPost());
            $form->isValid();
            var_dump($form->getMessages());
            if ($form->isValid()) {
     //\Doctrine\Common\Util\Debug::dump($listquest);
     //\Doctrine\Common\Util\Debug::dump($listquest->questions);die;
                $this->getEntityManager()->persist($listquest);
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
            return $this->redirect()->toRoute('home');
        }
        $question = $this->getEntityManager()
                         ->find('Question\Entity\Question',$id);
        
        if (!$this->_checkUserIsAuthorized($question->listquest)) {
            return $this->redirect()->toRoute('home');
        }
        
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
        $user = $this->zfcUserAuthentication()->getIdentity();
        
        foreach ($user->getRoles() as $role){
            if ($role->getRoleId() === 'admin'){
                return true;
            }
        }
        
        return $listquest && ($listquest->author->getId() === $user->getId());
    }
}

