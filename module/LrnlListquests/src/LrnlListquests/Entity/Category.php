<?php
namespace LrnlListquests\Entity;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 
use LrnlListquests\Entity\Provider\ProvidesNameField;


class Category extends EntityAbstract implements 
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
     * picture identification
     *
     * @var integer
     * @access protected
     */
    protected $pictureId; 
    
    /**
     * Description
     *
     * @var string
     * @access protected
     */
    protected $description;
    
    /**
     * Depth in hierarchie
     *
     * @var integer
     * @access protected
     */
    protected $depth;
    
    /**
     * Parent category
     *
     * @var integer
     * @access protected
     */
    protected $parent;
    
    protected $inputFilter;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setPictureId($pictureId)
    {
        $this->pictureId = $pictureId;
        return $this;
    }
    
    public function getPictureId()
    {
        return $this->pictureId;
    }
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }
    
    public function getDepth()
    {
        return $this->depth;
    }
    
    public function setParent(Category $parent)
    {
        $this->parent = $parent;
        return $this;
    }
    
    public function getParent()
    {
        return $this->parent;
    }
    
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function __toString()
    {
        return $this->getName();
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
                'name'     => 'description',
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
                            'max'      => 250,
                        ],
                    ],
                ],
            ]));
            $inputFilter->add($factory->createInput([
                'name'     => 'depth',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name'    => 'Int'],
                ],
            ]));
            $inputFilter->add($factory->createInput([
                'name'     => 'pictureId',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}