<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Entity\Question;          
use Question\Form\QuestionForm;     
use Question\Entity\Listquest;     


class AdminController extends AbstractActionController
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    
    public function indexAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }
       
        return [
            'listId'    => $id
        ];
    }

    public function addAction()
    {
        $listId = (int) $this->params()->fromRoute('id', 0);
        if (!$listId) {
            return $this->redirect()->toRoute('home');
        }
        
        $listquest = $this  ->getEntityManager()
                            ->getRepository('Question\Entity\Listquest')
                            ->find($listId);
        if (!$this->_checkUserIsAuthorized($listquest)) {
            return $this->redirect()->toRoute('home');
        }
        
        $form = new QuestionForm();
        $form->get('submit')->setValue('Add');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $question = new Question();
            $form->setInputFilter($question->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {                
                $question->exchangeArray(array_merge(
                    $form->getData(),
                    ['listquest' => $listquest]
                ));
                $this->getEntityManager()->persist($question);
                $this->getEntityManager()->flush();

                // Redirect to list of questions
                return $this->redirect()->toRoute('list');
            }
        }
        return ['form' => $form,'listId' => $listId];        
        
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
        
    }
    
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }   
    
 
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    }
    
    protected function _checkUserIsAuthorized(Listquest $listquest)
    {
        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        return $listquest && ($listquest->author->getId() === $userId);
    }
}

