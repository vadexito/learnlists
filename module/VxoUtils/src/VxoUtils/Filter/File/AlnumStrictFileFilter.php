<?php

namespace VxoUtils\Filter\File;

use Zend\Filter\PregReplace;


class AlnumStrictFileFilter extends PregReplace
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
        $fileName = parent::filter(strtolower(trim($sourceInfo['filename'])));        
        $value['name'] = $sourceInfo['dirname']
            .$fileName.
            $sourceInfo['extension'];        
        
        return $value;
    }
}


