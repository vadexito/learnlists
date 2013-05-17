<?php
namespace LrnlListquests\Form\Fieldset;

use LrnlListquests\Entity\Tag;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class TagFieldset extends Fieldset implements 
    InputFilterProviderInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;
    
    public function __construct($name='tag',$options=NULL)
    {
        parent::__construct($name,$options);
    
        $this->add([
            'name' => 'tag',
            'options' => [
                'label' => _('Tag'),
            ],                    
            'attributes' => [
                'type'  => 'text',
                'id' => 'tag',
                'autocomplete'    => 'off',
            ],
        ]);    
    }
    
    public function init()
    {
        if (!$this->getServiceLocator()){
            throw new ServiceNotFoundException('The form element manager was not initialized. Use the form element manager to initiate the fieldset');
        }
        
        $formElementManager = $this->getServiceLocator();
        $applicationServices = $formElementManager->getServiceLocator();
        $om = $applicationServices->get('Doctrine\ORM\EntityManager');       
        $object = new Tag();
        $this->setHydrator(new DoctrineHydrator($om, get_class($object)))
             ->setObject($object);
    }

    /**
     * @return array
     */    
    public function getInputFilterSpecification()    
    {
        return [
            'tag' => [
                'required' => true,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 20,
                        ],
                    ],
                ],
            ],
        ];
    }
}