<?php

namespace Question\Service;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Doctrine\Common\Persistence\ObjectManager;
use Question\Entity\Listquest;
use ZfcUser\Entity\UserInterface;
use DateTime;

class ListquestService
{
    protected $repository;
    
    protected $user;
    
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om,UserInterface $user = NULL)
    {
        $this->setObjectManager($om);
        $this->repository = $om->getRepository('Question\Entity\Listquest');
        $this->user = $user;
    }
    
    public function generateNewListquest()
    {
        $listquest = new Listquest();
        
        $listquest->setCreationDate(new DateTime());
        
        if ($this->user){
            $listquest->author = $this->user;
        }
        
        return $listquest;
    }

    public function fetchAll()
    {
        return $this->repository->findAll();
    }
    
    public function fetchById($id)
    {
        return $this->getObjectManager()->find('Question\Entity\Listquest',$id);
    }

    public function insertListquest(Listquest $listquest)
    {
        $this->getObjectManager()->persist($listquest);
        $this->getObjectManager()->flush();
    }
    
    public function updateListquest(Listquest $listquest)
    {
        $this->getObjectManager()->flush();
    }
    
    public function deleteListquest(Listquest $listquest)
    {
        $this->getObjectManager()->remove($listquest);
        $this->getObjectManager()->flush();
    }
}