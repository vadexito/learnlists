<?php
namespace Application\InputFilter;

use Zend\InputFilter\FileInput;
use Zend\Validator\File\IsImage;
use Zend\Validator\File\Size;

class PictureInputFilter extends FileInput
{
    public function __construct($targetUpload,$name = 'picture')
    {
        parent::__construct($name);
        
        $this->setRequired(true);
        $this->getValidatorChain()
             ->attach(new IsImage(),true)
             ->attach(new Size([
                'min' => '1kB',
                'max' => '0.5MB'
            ]));
        $this->getFilterChain()->attachByName('filerenamealnumstrict');
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