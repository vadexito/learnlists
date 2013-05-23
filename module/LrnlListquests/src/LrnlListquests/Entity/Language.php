<?php
namespace LrnlListquests\Entity;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 
use LrnlListquests\Entity\Provider\ProvidesNameField;
use VxoUtils\Entity\EntityAbstract;

class Language extends EntityAbstract implements 
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
     * code of the language
     *
     * @var string
     * @access protected
     */
    protected $code;    
    
    protected $inputFilter;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
    
    public function getCode()
    {
        return $this->code;
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
                'name'     => 'code',
                'required' => false,
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
                            'max'      => 10,
                        ],
                    ],
                ],
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}