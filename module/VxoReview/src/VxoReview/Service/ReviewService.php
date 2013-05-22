<?php

namespace VxoReview\Service;

use Doctrine\Common\Persistence\ObjectManager;
use ZfcUser\Entity\UserInterface;
use VxoReview\Entity\ReviewInterface;
use Application\Service\AbstractDoctrineEntityService;
use VxoReview\Exception\InvalidArgumentException;
use DateTime;

class ReviewService extends AbstractDoctrineEntityService
{
    protected $user;
    
    public function __construct(ObjectManager $om,$entityClass,
        UserInterface $user = NULL)
    {
        parent::__construct($om,$entityClass);
        $this->user = $user;
    }
    
    public function insert($review)
    {
        if (!($review instanceof ReviewInterface)){
            throw new InvalidArgumentException('The class to insert should implement review interface.');
        }
        
        $review->setCreationDate(new DateTime());
        
        if ($this->user){
            $review->author = $this->user;
        }
        
        return parent::insert($review);
    }
    
    public function update($review)
    {
        if (!($review instanceof ReviewInterface)){
            throw new InvalidArgumentException('The class to insert should implement review interface.');
        }
        
        return parent::update($review);
    }
}