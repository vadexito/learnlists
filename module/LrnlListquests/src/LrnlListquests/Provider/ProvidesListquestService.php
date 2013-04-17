<?php

namespace LrnlListquests\Provider;

use LrnlListquests\Service\ListquestService;

trait ProvidesListquestService
{
    protected $_service = NULL;
    
    public function setListquestService(ListquestService $service)
    {
        $this->_service = $service;
        return $this;
    }
    
    public function getListquestService()
    {
        return $this->_service;
    }
}