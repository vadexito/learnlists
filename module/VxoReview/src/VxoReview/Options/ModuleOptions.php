<?php

namespace VxoReview\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions 
{
    protected $reviewEntityClass = 'VxoReview\Entity\Review';
    
    protected $redirectRoute = 'home';
    
    protected $maxRating = 10;
    
    public function getReviewEntityClass()
    {
        return $this->reviewEntityClass;        
    }
    
    public function setReviewEntityClass($reviewEntityClass)
    {
        $this->reviewEntityClass = $reviewEntityClass;  
        return $this;
    }
    
    public function getMaxRating()
    {
        return $this->maxRating;        
    }
    
    public function setMaxRating($maxRating)
    {
        $this->maxRating = $maxRating;  
        return $this;
    }
    
    public function getRedirectRoute()
    {
        return $this->redirectRoute;        
    }
    
    public function setRedirectRoute($redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;  
        return $this;
    }
}
