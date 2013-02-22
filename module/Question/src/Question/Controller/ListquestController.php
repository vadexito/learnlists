<?php

namespace Question\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Question\Entity\Listquest;          
use Question\Form\ListquestForm; 
use ZfrForum\Entity\Post;
use ZfrForum\Entity\Thread;
use ZfrForum\Entity\Category;

class ListquestController extends AbstractActionController
{
    protected $listTable;
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $em;
    
    public function indexAction()
    {
        $rep = $this->getEntityManager()->getRepository('Question\Entity\Listquest');
        
        $ratingService = $this->getServiceLocator()->get('wtrating.service');
        
        
        
//        $commentService = $this->getServiceLocator()->get('ZfrForum\Service\ThreadService');
//        $postService = $this->getServiceLocator()->get('ZfrForum\Service\PostService');         
//        
//        $post = new Post();
//        $post->setContent('voila');
//        //$post->setAuthor($this->zfcUserAuthentication()->getIdentity());
//        $post->setLastModifiedAt(new \Zend\Stdlib\DateTime());
//        
//        //$this->zfcUserAuthentication()->getIdentity()->setIp($_SERVER['REMOTE_ADDR']);
//        //$this->zfcUserAuthentication()->getIdentity()->setLastActivityDate(new \Zend\Stdlib\DateTime());
//        $this->getEntityManager()->flush();
//        
//        $thread = new Thread;
//        $category = new Category();
//        $category->setName('question');
//        $category->setPosition(2);
//        
//        $thread->setTitle('t');
//        $thread->setCategory($category);
//        $thread->setCreatedAt(new \Zend\Stdlib\DateTime());
//        //$thread->setCreatedBy($this->zfcUserAuthentication()->getIdentity());
//        
//        $this->getEntityManager()->persist($category);
//        $this->getEntityManager()->persist($thread);
//        $commentService->addPost($thread,$post);
        
        
        
        return [
            'lists'  => $rep->findAll(),
            'ratingService' => $ratingService
        ];
    }
    
    public function showAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('home');
        }        
        return [
            'list'    => $this->getEntityManager()->find(
                'Question\Entity\Listquest',
                $id
            )
        ];
    }
    
    public function addAction()
    {
        $form = new ListquestForm();
        $form->get('submit')->setValue(_('Add'));
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $listquest = new Listquest(
                $this->getEntityManager(),
                $this->zfcUserAuthentication()->getIdentity()
            );
            $form->setInputFilter($listquest->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $listquest->exchangeArray($form->getData());
                $this->getEntityManager()->persist($listquest);
                $this->getEntityManager()->flush();

                // Redirect to list of questions
                return $this->redirect()->toRoute('list');
            }
        }
        return ['form' => $form];
        
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
            $ratingService->persist($rating);
        }
        
        return $this->redirect()->toRoute('list');
        
    }
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }
        return $this->em;
    } 
}

