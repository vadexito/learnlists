<?php 

namespace LrnlCategory\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlCategory\Entity\CategoryInterface as Category;

class Categories extends AbstractHelper
{
    protected $_lines;
    
    public function __invoke(array $categories)
    {
        if (!$categories){
            return '';
        }  
        $lines = [];
        foreach ($categories as $category){
            $lines[] = $this->renderCategory($category);
        }
        
        $this->_lines = $lines;
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->getView()->htmlList($this->_lines,false,[],false);
    }
    
    public function renderCategory(Category $category)
    {
        $urlAddImage = $this->getView()->url(
            'lrnlcategory/editpicture',
            ['id' => $category->getId()]
        );
        $linkAddImage = '<div class="pull-right"><a href="'.$urlAddImage.'">'.'Add or change picture</a></div>';        
        $line = $category->getName().', '.$category->getDescription().$linkAddImage;
        
        return $line;
    }
}