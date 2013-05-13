<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Entity\Listquest;
use LrnlListquests\Options\ModuleOptions;

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
        if ($listquest->getPictureId()){
            return $this->getView()->getFileById($listquest->getPictureId())->getUrl();
        }        
        return $this->getCategoryUrl($listquest->getCategory());
    }
    
    public function getCategoryUrl($category)
    {
        $dirCategoryThumbnail = $this->options->getCategories()['pictureDirectory'];
        return $dirCategoryThumbnail
        . ($category ? $this->getView()->escapeHtml($category) : 'empty').'.jpg';
    }
    
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    
}