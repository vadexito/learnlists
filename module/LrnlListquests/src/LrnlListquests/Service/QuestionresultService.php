<?php

namespace LrnlListquests\Service;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Doctrine\Common\Persistence\ObjectManager;
use LrnlListquests\Exception\ServiceException;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use LrnlListquests\Options\ModuleOptions;

class QuestionresultService
{
    protected $repository;
    protected $options;
    protected $_listquestService;
    protected $_classEntity;
    
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om,ModuleOptions $options)
    {
        $this->setObjectManager($om);
        $this->options = $options;
        $this->_classEntity = $options->getQuestionresultEntityClass();
        $this->repository = $om->getRepository($this->_classEntity);
    }
    
    public function createNewEntity(Array $data = NULL)
    {
        $questionresult = new $this->_classEntity;
        if ($data === NULL){            
            return $questionresult;
        }
        $hydrator = new DoctrineHydrator(
            $this->getObjectManager(),
            $this->_classEntity
        );
        
        $dataHydrate = [
            'question' => $data['questionId'],
            'round' => $data['roundId'],
            'answerType' => $data['answerType'],
            'points' => $this->calculateScore($data['answerType']),
        ];
        
        $hydrator->hydrate($dataHydrate,$questionresult);
        
        $questionresult->getRound()->setScore(
            $questionresult->getRound()->getScore() + $questionresult->points
        );
        $this->getObjectManager()->persist($questionresult);
        
        try {
            
            $this->getObjectManager()->flush();
        } catch (Exception $e) {
            throw new \Exception('Doctrine creating failed');
        }
        
        return $questionresult;
    }
    
    public function calculateScore($answerType)
    {
        $pointTable = $this->options->getScoreTable();        
        return $pointTable[(string)$answerType];
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