<?php
namespace LrnlListquests\Hydrator;

use Zend\Stdlib\Hydrator\HydratorInterface;
use LrnlListquests\Provider\ProvidesListquestService;
use FileBank\Manager;
use LrnlListquests\Options\ModuleOptions;
use WebinoImageThumb\Module as Thumbnailer;
use LrnlListquests\Exception\HydratorException;


class Picture implements HydratorInterface
{
    protected $_fileBankService;
    protected $_thumbnailer;
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
        if (!isset($value['size'])){
            throw new HydratorException('The value from the form should have a size field for the file');
        }
        if ($value['size'] >0){
            if (!isset($value['name'])){
                throw new HydratorException('The value from the form should have a name field for the file');
            }
            
            $name = $value['name'];
            $dir = $this->options->getTmpPictureUploadDir();
            $pictureName = $dir.$name;
            $thumbnailName = $dir.'thumb'.$name;
            
            $fileBank = $this->getFileBankService();
            $fileBank->save($pictureName,['listquest']);
            
            $thumb = $this->getThumbnailer()->create($thumbnailName,[]);
            $thumb->resize(114,70);            
            $thumb->save($thumbnailName);
            
            $pictureId = $fileBank->save($thumbnailName,['listquest'])->getId();
            $listquest->setPictureId($pictureId);
            $this->getListquestService()->updateListquest($listquest);
            
            unlink($pictureName);
            unlink($thumbnailName);
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
    
    public function setThumbnailer(Thumbnailer $thumbnailer)
    {
        $this->_thumbnailer = $thumbnailer;
        return $this;
    }
    
    public function getThumbnailer()
    {
        return $this->_thumbnailer;
    }
}

