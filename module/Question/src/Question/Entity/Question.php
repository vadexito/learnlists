<?php
namespace Question\Entity;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;   
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;

/**
 *
 * @property int $id
 * @property string $text
 * @property string $answer
 * @property string $tip
 * @property Listquest $listquest
 */

class Question extends EntityAbstract implements InputFilterAwareInterface
{
    /**
     * Primary Identifier
     *
     * @var integer
     * @access protected
     */
    protected $id;

    /**
     *
     * @var string
     * @access protected
     */
    protected $text;
    /**
     *
     * @var string
     * @access protected
     */
    protected $answer;
    /**
     *
     * @var string
     * @access protected
     */
    protected $tip;

    /**
    * 
    * @var Question\Entity\ListQuest
    */
    protected $listquest;
    
    protected $inputFilter;
    
    public function toArray()
    {
        $array = [
            'id'        => $this->id,
            'text'      => $this->text,
            'answer'    => $this->answer,
            'tip'       => $this->tip,
        ];
        
        if ($this->listquest) {
            $array['listquest'] = [
                'id'    => $this->listquest->id,
                'title' => $this->listquest->title,
            ];
        }
        
        return $array;
        
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function exchangeArray(array $data,EntityManager $entityManager = NULL)
    {
        foreach (['id','text','answer','tip'] as $property)
        {
            $this->$property = (isset($data[$property])) ? 
                $data[$property] : null;
        }
        
        if ($data['listId'] && $entityManager){
            $this->listquest = $entityManager->find('Question\Entity\Listquest',$data['listId']);
        }
    }
    
    // Add content to this method:
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
                'name'     => 'listId',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]));

            $inputFilter->add($factory->createInput([
                'name'     => 'text',
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
                            'min'      => 1,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'answer',
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
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'tip',
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
                            'min'      => 1,
                            'max'      => 100,
                        ],
                    ],
                ],
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}