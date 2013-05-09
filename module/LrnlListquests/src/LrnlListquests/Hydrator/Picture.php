<?php
namespace LrnlListquests\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;
use LrnlListquests\Provider\ProvidesListquestService;
use FileBank\Manager;
use LrnlListquests\Options\ModuleOptions;


class Picture implements HydratorInterface
{
    protected $_fileBankService;
    protected $options;
    
    use ProvidesListquestService;
    
    public function __construct(ModuleOptions $options)
    {
        $this->options = $options;
    }
    
    public function extract($listquest)
    {
        return ['pictureId' => $listquest->getPictureId()];
    }

    public function hydrate(array $value,$listquest)
    {
        if ($value['size'] >0){

            $name = $value['name'];
            $dir = $this->options->getTmpPictureUploadDir();

            $fileBank = $this->getFileBankService();
            $fileBank->save($dir.$name,['listquest']);

            $pictureId = $fileBank->save($dir.$name,['listquest'])->getId();
            $listquest->setPictureId($pictureId);
            $this->getListquestService()->updateListquest($listquest);
        }
    }
    
    public function setFileBankService(Manager $fileBankService)
    {
        $this->_fileBankService = $fileBankService;
        return $this;
    }
    
    public function getFileBankService()
    {
        return $this->_fileBankService;
    }
}

