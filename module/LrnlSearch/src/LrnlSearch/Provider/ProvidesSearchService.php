<?php

namespace LrnlSearch\Provider;

use LrnlSearch\Service\SearchService;

trait ProvidesSearchService
{
    protected $_searchService = NULL;
    
    public function setSearchService(SearchService $service)
    {
        $this->_searchService = $service;
        return $this;
    }
    
    public function getSearchService()
    {
        return $this->_searchService;
    }
}