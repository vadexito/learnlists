<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Entity\Listquest;

class ListquestPictureUrl extends AbstractHelper
{
    /**
     *
     * @var type LrnlListquests\Options\ModuleOptions
     */
    
    protected $options;
    
    public function __invoke(Listquest $listquest = NULL)
    {
        if ($listquest === NULL){
            return $this;
        }
        if ($listquest->getPictureId() &&
            $this->getView()->fileBank()->getFileById($listquest->getPictureId())->getUrl()){
            return $this->getView()->fileBank()->getFileById($listquest->getPictureId())->getUrl();
        }        
        return $this->getCategoryUrl($listquest->getCategory());
    }
    
    public function getCategoryUrl($category)
    {
        $category = 'French';
        $dirCategoryThumbnail = $this->getCategoryPictureDirectory();            
        if ($category){
            $file = $dirCategoryThumbnail
                . $this->getView()->escapeHtml($category)
                .'.jpg';
            if (file_exists('./module/Application'.$file)){
                return $file;
            }
        }
        return $dirCategoryThumbnail.'empty.jpg';
    }
    
    public function setCategoryPictureDirectory($categoryPictureDirectory)
    {
        $this->categoryPictureDirectory = $categoryPictureDirectory;
        return $this;
    }
    
    public function getCategoryPictureDirectory()
    {
        return $this->categoryPictureDirectory;
    }
    
    
}