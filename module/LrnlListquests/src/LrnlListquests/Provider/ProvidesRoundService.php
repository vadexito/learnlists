<?php

namespace LrnlListquests\Provider;

use LrnlListquests\Service\RoundService;

trait ProvidesRoundService
{
    protected $_roundService = NULL;
    
    public function setRoundService(RoundService $service)
    {
        $this->_roundService = $service;
        return $this;
    }
    
    public function getRoundService()
    {
        return $this->_roundService;
    }
}