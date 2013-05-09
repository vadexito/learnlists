<?php
namespace LrnlListquests\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\FileInput;

class Picture extends FileInput
{
    public function __construct($name = 'picture',$targetUpload)
    {
        parent::__construct($name);
        
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