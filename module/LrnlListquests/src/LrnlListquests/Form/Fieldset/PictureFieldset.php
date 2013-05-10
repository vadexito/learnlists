<?php
namespace LrnlListquests\Form\Fieldset;

use Zend\Form\Fieldset;
use LrnlListquests\Form\Element\Picture;

class PictureFieldset extends Fieldset
{
    public function __construct($name = 'picture')
    {
        parent::__construct($name);        
        $this->add(new Picture('pictureId'));
    }
}