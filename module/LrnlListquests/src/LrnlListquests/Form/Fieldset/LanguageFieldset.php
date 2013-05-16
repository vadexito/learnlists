<?php
namespace LrnlListquests\Form\Fieldset;

use DoctrineModule\Persistence\ProvidesObjectManager;
use Zend\Form\Fieldset;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;

class LanguageFieldset extends Fieldset implements ObjectManagerAwareInterface
{
    use ProvidesObjectManager;
    
    protected $_entityClass = 'LrnlListquests\Entity\Language';
    
    public function __construct($om,$name = 'language',$options = NULL)
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
                'id'    => 'language_name',
                'class' => 'chzn-select',
            ],
            'options' => [
                'object_manager' => $om,
                'target_class'   => $entityClass,
                'property'       => 'name',
                'label' => _('Language'),
            ],
        ]);
    }
}