<?php

namespace LrnlListquests\Service;

use LrnlListquests\Options\ModuleOptions;


class LanguageService
{
    protected $options;
    
    public function __construct(ModuleOptions $options)
    {
        $this->options = $options;        
    }
    
    public function fetchAll()
    {
        return $this->options->getListquestItems()['language']['list'];
    }
}