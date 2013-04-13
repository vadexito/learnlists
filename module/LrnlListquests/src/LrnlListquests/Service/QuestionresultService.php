<?php

namespace LrnlListquests\Service;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Doctrine\Common\Persistence\ObjectManager;
use LrnlListquests\Entity\Listquest;
use ZfcUser\Entity\UserInterface;
use DateTime;
use LrnlListquests\Exception\ServiceException;

class QuestionresultService
{
    protected $repository;
    protected $_listquestService;
    protected $_classEntity;
    
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om,$questionresult)
    {
        $this->setObjectManager($om);
        $this->_classEntity = get_class($questionresult);
        $this->repository = $om->getRepository($this->_classEntity);
    }
    
    public function createNewEntity(Array $data = NULL)
    {
        $questionresult = new $this->_classEntity;
        if ($data === NULL){            
            return $questionresult;
        }
        $questionresult->question = $this->getObjectManager()->getRepository('LrnlListquests\Entity\Question')
                                 ->find($data['questionId']);
        $questionresult->round = $this  ->getObjectManager()->getRepository('LrnlListquests\Entity\Round')
                                        ->find($data['roundId']);  
        $questionresult->answerType = $data['answerType'];
        
        $this->getObjectManager()->persist($questionresult);
        
        try {
            
            $this->getObjectManager()->flush();
        } catch (Exception $e) {
            throw new \Exception('Doctrine creating failed');
        }
        
        return $questionresult;
    }
    
    public function fetchById($id)
    {
        return $this->repository->find($id);
    }
    
    public function deleteQuestionresult($id)
    {
        if (!$this->fetchById($id)) {
           return; 
        }
        
        $this->getObjectManager()->remove($this->fetchById($id));
        try {
            
            $this->getObjectManager()->flush();
        } catch (\Exception $e) {
            throw new ServiceException('Doctrine deleting failed');
        }
    }
}