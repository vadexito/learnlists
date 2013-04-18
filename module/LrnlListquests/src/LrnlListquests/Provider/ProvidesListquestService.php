<?php

namespace LrnlListquests\Provider;

use LrnlListquests\Service\ListquestService;

trait ProvidesListquestService
{
    protected $_listquestService = NULL;
    
    public function setListquestService(ListquestService $service)
    {
        $this->_listquestService = $service;
        return $this;
    }
    
    public function getListquestService()
    {
        return $this->_listquestService;
    }
}