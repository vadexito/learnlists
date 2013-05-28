<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Entity\Listquest;

class ListquestPictureUrl extends AbstractHelper
{
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
        $pictureId = $category->getPictureId();
        if ($pictureId && 
            $this->getFileBankService()->getFileById($pictureId)->getUrl()){
            return $this->getFileBankService()->getFileById($pictureId)->getUrl();
        }
        
        $empty = $this->getFileBankService()->getFilesByKeywords(['category','empty']);
        if (count($empty)>0){
            return $empty[0]->getUrl();
        } else {
            return '';
        }
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