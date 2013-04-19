<?php
namespace LrnlUser\Form;

use DoctrineModule\Form\Element\ObjectSelect;
use Doctrine\Common\Persistence\ObjectManager;

class RoleElement extends ObjectSelect
{
    public function __construct($name = 'role',ObjectManager $objectManager)
    {
        parent::__construct($name);
    
        $this->setOptions([
            'label' => _('Role'),
            'object_manager' => $objectManager,
            'target_class'   => 'ZfcUserLL\Entity\Role',
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