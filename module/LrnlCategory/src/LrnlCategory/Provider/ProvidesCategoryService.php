<?php

namespace LrnlCategory\Provider;

use LrnlCategory\Service\CategoryService;

trait ProvidesCategoryService
{
    protected $categoryService = null;
    
    public function setCategoryService(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
        return $this;
    }
    
    public function getCategoryService()
    {
        return $this->categoryService;
    } 
}