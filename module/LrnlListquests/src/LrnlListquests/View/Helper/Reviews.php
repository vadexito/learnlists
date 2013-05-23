<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use VxoReview\Entity\ReviewInterface as Review;

class Reviews extends AbstractHelper
{
    protected $_reviewLines;
    
    public function __invoke(array $reviews)
    {
        if (!$reviews){
            return '';
        }  
        $reviewsLines = [];
        foreach ($reviews as $review){
            $reviewsLines[] = $this->renderReview($review);
        }
        
        $this->_reviewLines = $reviewsLines;
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->getView()->htmlList($this->_reviewLines);
    }
    
    public function renderReview(Review $review)
    {
        return $review->getText();
    }
}