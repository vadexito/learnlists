<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use Doctrine\Common\Persistence\ObjectManager;

class ChangePictureForm extends Form
{
    public function __construct($name = 'ChangePictureForm',$options = NULL)
    {
        parent::__construct($name,$options);
        $this->setAttribute('method', 'post');
        
        $this->add([
            'name' => 'submit',
            'type'  => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => 'Save changes',
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],
        ]);
    }
}