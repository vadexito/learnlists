<?php
namespace LrnlListquests\Form;

use LrnlListquests\Entity\Question;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ProvidesObjectManager;


class QuestionFieldset extends Fieldset implements InputFilterProviderInterface
{
    use ProvidesObjectManager;
    
    public function __construct(ObjectManager $om)
    {
        parent::__construct('question');
        $this->setObjectManager($om);
        
        $question = new Question();
        $this->setHydrator(new DoctrineHydrator($om, get_class($question)))
             ->setObject($question);
    
    
        $this->add([
            'name'      => 'id',
            'attributes'=> [
                'type'  => 'hidden',
            ],
        ]);
        
        $this->add([
            'name' => 'text',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'text',
                'autocomplete'    => 'off',
                'class' => 'input-xxlarge',
                'required' => 'required'
            ],
            'options' => [
                'label' => _('Text')
            ],
        ]);
        
        $this->add([
            'name' => 'answer',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'answer',
                'class' => 'input-medium',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => _('Answer')
            ],
        ]);
        
        $this->add([
            'name' => 'tip',
            'attributes' => [
                'type'  => 'text',
                'id'    => 'tip',
                'autocomplete'    => 'off',
            ],
            'options' => [
                'label' => _('Tip')
            ],
        ]);
    }

   /**
     * @return array
     */    
    public function getInputFilterSpecification()    
    {
        return [
            'id' => [
                'required' => false,
            ],
            'text' => [
                'required' => true,
            ],
            'answer' => [
                'required' => false,
            ],
            'tip' => [
                'required' => false,
            ],
        ];
    }
}