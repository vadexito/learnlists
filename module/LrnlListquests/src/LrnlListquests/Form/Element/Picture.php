<?php
namespace LrnlListquests\Form\Element;

use Zend\Form\Element\File;
use Zend\InputFilter\InputProviderInterface;

class Picture extends File
{
    public function __construct($name = 'listquest_picture')
    {
        parent::__construct($name);
        
        $this->setLabel(_('Picture for your list'))
             ->setAttributes([
                'id' => 'listquest_picture',
            ]);
    }
}