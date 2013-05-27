<?php

namespace VxoReview\Service;

use Doctrine\Common\Persistence\ObjectManager;
use ZfcUser\Entity\UserInterface;
use VxoReview\Entity\ReviewInterface;
use Application\Service\AbstractDoctrineEntityService;
use VxoReview\Exception\InvalidArgumentException;
use DateTime;
use VxoReview\Options\ModuleOptions;

class ReviewService extends AbstractDoctrineEntityService 
    implements ReviewServiceInterface
{
    protected $user;
    
    protected $maxRating;
    
    public function __construct($maxRating,ObjectManager $om,$entityClass,
        UserInterface $user = NULL)
    {
        parent::__construct($om,$entityClass);
        $this->user = $user;
        $this->maxRating = $maxRating;
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
    
    public function fetchByReviewedItem($reviewedItemId)
    {
        $reviews = $this->getRepository()->findBy(['reviewedItem' => $reviewedItemId]);        
        return $reviews;
    }
    
    public function getRatingMax()
    {
        return $this->maxRating;
    }
    
    public function getRating($entityId)
    {
        $reviews = $this->getRepository()->findBy(['reviewedItem' => $entityId]);
        if (!$reviews){
            return false;
        }
        $sum = 0;        
        foreach ($reviews as $review){
           $sum +=$review->getRating(); 
        }
        return $sum / (count($reviews));
    }
    
    public function getReviewCount($entityId)
    {
        $reviews = $this->getRepository()->findBy(['reviewedItem' => $entityId]);
        return count($reviews);
    }
}