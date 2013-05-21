<?php

namespace LrnlListquests\Filter\File;

use Zend\Filter\PregReplace;


class LowerAlnum extends PregReplace
{
    public function __construct($options = null)
    {
        parent::__construct([
            'pattern'     => '/[^a-z^1-9]/',
            'replacement' => '',
        ]);
    }

    public function filter($value)
    {
        $sourceInfo = pathinfo($value['name']);
        $fileName = parent::filter(strtolower($sourceInfo['filename']));        
        $value['name'] = $sourceInfo['dirname']
            .$fileName.
            $sourceInfo['extension'];        
        
        return $value;
    }
}


