<?php
namespace LrnlListquests\Form\Fieldset;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Form\Fieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class CategoryFieldset extends Fieldset implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    
    protected $_entityClass = 'LrnlListquests\Entity\Category';
    
    public function __construct($om,$name = 'category',$options = NULL)
    {
        parent::__construct($name,$options);
        
        $entityClass = $this->_entityClass;
        $doctrineHydrator = new DoctrineHydrator(
            $om,
            $entityClass
        );
        $this->setHydrator($doctrineHydrator);
        $this->setObject(new $entityClass); 
        
        $this->add([
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'name' => 'id',
            'attributes' => [
                'id'    => 'category_name',
                'class' => 'chzn-select',
            ],
            'options' => [
                'object_manager' => $om,
                'target_class'   => $entityClass,
                'property'       => 'name',
                'label' => _('Category'),
            ],
        ]);
    }
}