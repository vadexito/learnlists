<?php

namespace LrnlListquests\Service;

use LrnlListquests\Options\ModuleOptions;


class CategoryService
{
    protected $options;
    
    public function __construct(ModuleOptions $options)
    {
        $this->options = $options;        
    }
    
    public function fetchAll()
    {
        return $this->options->getListquestItems()['category']['list'];
    }
}