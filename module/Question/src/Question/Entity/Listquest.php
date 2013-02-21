<?php
namespace Question\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;   

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="listquests")
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $rules
 * @property string $level
 * @property datetime $creationDate
 * @property ArrayCollection $tags
 */

class Listquest extends EntityAbstract
{
    /**
     * Primary Identifier
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     * @access protected
     */
    protected $id;

    /**
     * Title of each list
     *
     * @ORM\Column(type="string")
     * @var string
     * @access protected
     */
    protected $title;
    
    /**
     * Rules for the quiz
     *
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @access protected
     */    
    protected $rules;
    
    /**
     * Level of the quiz
     *
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @access protected
     */
    protected $level;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="ZfcUserLL\Entity\User")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id")
     * 
     */
    protected $author;
    
    /**
     * 
     * @ORM\Column(name="creation_date",type="datetime")
     * @var datetime
     * @access protected
     * 
     */
    protected $creationDate;
    
    /**
     * 
     * @ORM\OneToMany(targetEntity="Question",mappedBy="listquest")
     * 
     */
    protected $questions;
    
    /**
     * 
     * @ORM\ManyToMany(targetEntity="Tag",mappedBy="listquests")     * 
     * 
     */
    protected $tags;    
    
    protected $inputFilter;
    
    protected $_em;
    
    protected $_user;
    
    public function __construct($em = NULL,$user = NULL) 
    {
        $this->questions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        if ($em){
            $this->_em = $em;
        }
        
        if ($user){
            $this->_user = $user;
        }
    }
    
    public function addTag(Tag $tag)
    {
        $tag->addListquest($this);
        $this->tags[] = $tag;
    }
    
    public function toArray()
    {
        $questions = [];
        foreach ($this->questions as $question) {
            $questions[] = $question->toArray();
        }
        
        return [
            'id' => $this->id,
            'title' => $this->title,
            'rules' => $this->rules,
            'creationDate' => $this->creationDate,
            'questions' => $questions,
        ];
    }
    
    public function exchangeArray($data)
    {
        foreach (['id','title','level','rules'] as $property)
        {
            $this->$property = (isset($data[$property])) ? 
                $data[$property] : null;
        }
        
        if (isset($data['tags'])){
            $tag = new Tag();
            $tag->tag = $data['tags'];
            $this->_em->persist($tag);
            $this->addTag($tag);        
        }        
        
        $this->author = $this->_user;
        $this->creationDate = new \Zend\Stdlib\DateTime();
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
                'name'     => 'title',
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
                            'max'      => 100,
                        ],
                    ],
                ],
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'rules',
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