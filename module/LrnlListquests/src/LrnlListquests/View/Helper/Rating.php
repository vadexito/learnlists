<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Service\ListquestService;
use WtRating\Service\RatingService;
use LrnlListquests\Provider\ProvidesListquestService;
use ZendSearch\Lucene\Search\QueryHit;

class Rating extends AbstractHelper
{
    protected $_ratingService;
    
    public function __invoke($listquest)
    {
        $amount = $this ->getRatingService()
                        ->getRatingSet($listquest->id)
                        ->getAmount();
        
        return round($amount);
    }
    
    public function setRatingService(RatingService $service)
    {
        $this->_ratingService = $service;
        return $this;
    }
    
    public function getRatingService()
    {
        return $this->_ratingService;
    }
}