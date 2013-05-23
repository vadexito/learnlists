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
    
    protected $fileBankService;
    
    public function __invoke(Listquest $listquest = NULL)
    {
        if ($listquest === NULL){
            return $this;
        }
        if ($listquest->getPictureId() &&
            $this->getFileBankService()->getFileById($listquest->getPictureId())->getUrl()){
            return $this->getFileBankService()->getFileById($listquest->getPictureId())->getUrl();
        }        
        return $this->getCategoryUrl($listquest->getCategory());
    }
    
    public function getCategoryUrl($category)
    {
        return $this->getFileBankService()->getFileById(54)->getUrl();
        
        
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
    
    public function getFileBankService() {
        return $this->fileBank;
    }

    /**
     * Set FileBank service.
     *
     * @param $service
     */
    public function setFileBankService($fileBank) {
        $this->fileBank = $fileBank;
        return $this;
    }
}