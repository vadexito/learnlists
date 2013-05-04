<?php
namespace LrnlUser\Form\Element;

use DoctrineModule\Form\Element\ObjectSelect as Select;
use Doctrine\Common\Persistence\ObjectManager;

class Role extends Select
{
    public function __construct($name = 'role',
            ObjectManager $objectManager,$roleEntityClass)
    {
        parent::__construct($name);
    
        $this->setOptions([
            'label' => _('You are...'),
            'object_manager' => $objectManager,
            'target_class'   => $roleEntityClass,
            'property'       => 'roleId',
            'is_method'      => true,
            'find_method'    => array(
                'name'   => 'findBy',
                'params' => array(
                    'criteria' => array('roleId' => ['teacher','student']),
                ),
            ),
        ]);
        
        $this->setAttributes([
            'id'    => 'role',
        ]);
    }
}