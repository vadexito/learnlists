<?php
namespace LrnlListquests\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\FileInput;

class Picture extends FileInput
{
    protected $_target = NULL;
    
    public function __construct($name = 'picture')
    {
        parent::__construct($name);
        
        $this->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'target'          => $this->getTargetUpload(),
                'overwrite'       => true,
                'use_upload_name' => true,
            ]
        );
    }
    
    public function setTargetUpload($target)
    {
        $this->_target = $target;
    }
    
    public function getTargetUpload()
    {
        return $this->_target;
    }
}