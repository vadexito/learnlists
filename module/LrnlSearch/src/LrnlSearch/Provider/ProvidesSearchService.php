<?php

namespace LrnlSearch\Provider;

use LrnlSearch\Service\SearchServiceInterface;

trait ProvidesSearchService
{
    protected $_searchService = NULL;
    
    public function setSearchService(SearchServiceInterface $service)
    {
        $this->_searchService = $service;
        return $this;
    }
    
    public function getSearchService()
    {
        return $this->_searchService;
    }
}