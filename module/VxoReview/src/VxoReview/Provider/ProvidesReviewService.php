<?php

namespace VxoReview\Provider;

use VxoReview\Service\ReviewService;

trait ProvidesReviewService
{
    protected $reviewService = null;
    
    public function setReviewService(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
        return $this;
    }
    
    public function getReviewService()
    {
        return $this->reviewService;
    } 
}