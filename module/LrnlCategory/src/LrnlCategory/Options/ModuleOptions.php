<?php

namespace LrnlCategory\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions 
{
    protected $categoryEntityClass = 'LrnlCategory\Entity\Category';
    
    protected $redirectRoute = 'home';
    
    protected $tmpPictureUploadDir = './data/tmpuploads/';
    
    public function getCategoryEntityClass()
    {
        return $this->categoryEntityClass;        
    }
    
    public function setCategoryEntityClass($categoryEntityClass)
    {
        $this->categoryEntityClass = $categoryEntityClass;  
        return $this;
    }
    
    public function getRedirectRoute()
    {
        return $this->redirectRoute;        
    }
    
    public function setRedirectRoute($redirectRoute)
    {
        $this->redirectRoute = $redirectRoute;  
        return $this;
    }
    
    public function getTmpPictureUploadDir()
    {
        return $this->tmpPictureUploadDir;        
    }
    
    public function setTmpPictureUploadDir($tmpPictureUploadDir)
    {
        $this->tmpPictureUploadDir = $tmpPictureUploadDir;  
        return $this;
    }
}
