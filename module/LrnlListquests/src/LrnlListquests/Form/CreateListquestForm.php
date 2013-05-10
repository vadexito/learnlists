<?php

namespace LrnlListquests\Form;

use Zend\Form\Form;
use Zend\Form\Element\Csrf;
use Doctrine\Common\Persistence\ObjectManager;

class CreateListquestForm extends Form
{
    public function __construct()
    {
        parent::__construct('listquestForm');
        $this->setAttribute('method', 'post');
        
        $this->add(new Csrf('csrf'));
   
        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type'  => 'submit',
                'value' => _('Check'),
                'id' => 'submitbutton',
                'class' => 'btn btn-primary',
            ],            
        ]);
        
        $this->setValidationGroup([
            'csrf',
            'listquest' => [
                'title',
                'description',
                'category',
                'language',
                'tags',
                'level',
                'rules',                
            ],            
            'picture' => ['pictureId'],
        ]);
    }
    
    
}