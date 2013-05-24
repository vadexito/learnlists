<?php
namespace Application\HydratorStrategy;

use Zend\Stdlib\Hydrator\Strategy\StrategyInterface;
use FileBank\Manager;
use WebinoImageThumb\Module as Thumbnailer;
use Application\Exception\InvalidArgumentException;


class PictureHydratorStrategy implements StrategyInterface
{
    protected $_fileBankService;
    protected $_thumbnailer;
    protected $tempDir;
    protected $keywords;
    
    public function __construct($tempDir,array $keywords = [])
    {
        $this->tempDir = $tempDir;
        $this->keywords = $keywords;
    }
    
    public function extract($value)
    {
        return $value;
    }

    public function hydrate($value)
    {
        if (!isset($value['size'])){
            throw new InvalidArgumentException('The value from the form should have a size field for the file');
        }
        if ($value['size'] >0){
            if (!isset($value['name'])){
                throw new InvalidArgumentException('The value from the form should have a name field for the file');
            }
            
            $name = $value['name'];
            $pictureName = $this->tempDir.$name;
            $thumbnailName = $this->tempDir.'thumb'.$name;
            
            $fileBank = $this->getFileBankService();            
            $thumb = $this->getThumbnailer()->create($pictureName,[]);
            $thumb->resize(114,70); 
            $thumb->save($thumbnailName);

            $pictureId = $fileBank->save($thumbnailName,$this->keywords)->getId();
            
            unlink($pictureName);
            unlink($thumbnailName);
            
            return $pictureId;
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

