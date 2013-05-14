<?php
namespace LrnlListquests\InputFilter;

use Zend\InputFilter\FileInput;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;

class Picture extends FileInput
{
    public function __construct($name = 'picture',$targetUpload)
    {
        parent::__construct($name);
        
        $this->setRequired(true);
        $this->getValidatorChain()
             ->attach(new IsImage(),true)
             ->attach(new Size([
                'min' => '1kB',
                'max' => '0.5MB'
            ]));
        
        $this->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'target'          => $targetUpload,
                'overwrite'       => true,
                'use_upload_name' => true,
            ]
        );
    }
}