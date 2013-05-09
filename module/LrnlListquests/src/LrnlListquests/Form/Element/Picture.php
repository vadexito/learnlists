<?php
namespace LrnlListquests\Form\Element;

use Zend\Form\Element\File;
use Zend\InputFilter\InputProviderInterface;

class Picture extends File
{
    public function __construct($name = 'picture')
    {
        parent::__construct($name);
        
        $this
            ->setLabel(_('Choose a picture for the category'))
            ->setAttributes([
                'id' => 'category_picture',
            ]);
    }
}