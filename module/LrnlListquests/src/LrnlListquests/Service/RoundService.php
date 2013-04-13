<?php

namespace LrnlListquests\Service;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Doctrine\Common\Persistence\ObjectManager;
use LrnlListquests\Entity\Listquest;
use ZfcUser\Entity\UserInterface;
use DateTime;
use LrnlListquests\Service\ListquestService;
use LrnlListquests\Exception\ServiceException;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class RoundService
{
    protected $repository;
    protected $user;  
    protected $_listquestService;
    protected $_classEntity;
    
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om,UserInterface $user = NULL,$round)
    {
        $this->setObjectManager($om);
        $this->repository = $om->getRepository(get_class($round));
        $this->user = $user;
        $this->_classEntity = get_class($round);
    }
    
    public function createNewEntity(Array $data = NULL)
    {
        $round = new $this->_classEntity;
        $round->user = $this->user;
        if ($data === NULL){            
            return $round;
        }
        $hydrator = new DoctrineHydrator(
            $this->getObjectManager(),
            $this->_classEntity
        );
        
        //date come from javascript in UGT timezone, they have to be converted
        //in local time to be stored in the database in local time
        $localTimeZone = (new DateTime())->getTimezone();
        
        $dataHydrate = [
            'listquest' => $data['listquestId'],
            'startDate' => (new DateTime($data['startDate']['date']))
                            ->setTimezone($localTimeZone)->getTimestamp(),
            'endDate' => (new DateTime($data['endDate']['date']))
                            ->setTimezone($localTimeZone)->getTimestamp(),
        ];
        
        $hydrator->hydrate($dataHydrate,$round);
        $this->getObjectManager()->persist($round);
        
        try {
            
            $this->getObjectManager()->flush();
        } catch (Exception $e) {
            throw new ServiceException('Doctrine creating failed');
        }
        
        return $round;
    }

    /**
     * 
     * @param int $listquestId
     * @return array of arrays (round)
     */
    public function fetchByUserByListquestId($listquestId)
    {
        $user = $this->user;
        if (!$user){
            return false;
        }     
        
        if (!is_int($listquestId)) {
            throw new ServiceException('Please provide an integer for the listquestId'.$listquestId.' provided');
        }
        
        $qb = $this->repository->createQueryBuilder('r');
        $rounds = $qb   ->join('r.user','u')
                        ->join('r.listquest','l')
                        ->where('u.id = :userId')
                        ->andWhere('l.id = :listquestId')
                        ->setParameter('userId',$user->getId())
                        ->setParameter('listquestId',$listquestId)
                        ->getQuery()->getResult();
        
        $result = [];
        foreach ($rounds as $round){
            $result[] = $round->toArray();
        };
        
        return $result;
    }
    
    public function fetchById($id)
    {
        return $this->repository->find($id);
    }
    
    public function deleteRound($id)
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