<?php

namespace LrnlListquests\Provider;

use LrnlListquests\Service\CategoryService;

trait ProvidesCategoryService
{
    protected $_categoryService = NULL;
    
    public function setCategoryService(CategoryService $service)
    {
        $this->_categoryService = $service;
        return $this;
    }
    
    public function getCategoryService()
    {
        return $this->_categoryService;
    }
}