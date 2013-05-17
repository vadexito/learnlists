<?php
namespace LrnlListquests\Entity;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 
use LrnlListquests\Entity\Provider\ProvidesNameField;


class Level extends EntityAbstract implements 
    InputFilterAwareInterface
{
    
    use ProvidesNameField;
    
    /**
     * Primary Identifier
     *
     * @var integer
     * @access protected
     */
    protected $id;

    /**
     * Name
     *
     * @var string
     * @access protected
     */
    protected $name;
    /**
     * relative difficulty of the level
     *
     * @var integer
     * @access protected
     */
    protected $difficulty;    
    
    protected $inputFilter;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
        return $this;
    }
    
    public function getDifficulty()
    {
        return $this->difficulty;
    }
    
    public function __toString()
    {
        return $this->getName();
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput([
                'name'     => 'id',
                'required' => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]));

            $inputFilter->add($factory->createInput([
                'name'     => 'name',
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
                            'max'      => 50,
                        ],
                    ],
                ],
            ]));
            $inputFilter->add($factory->createInput([
                'name'     => 'difficulty',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
                'validators' => [
                    [
                        'name'    => 'Between',
                        'options' => [
                            'min'      => 0,
                            'max'      => 100,
                            'inclusive' => true
                        ],
                    ],
                ],
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}