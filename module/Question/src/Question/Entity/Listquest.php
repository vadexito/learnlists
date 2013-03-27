<?php
namespace Question\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 
use ZfcUser\Entity\UserInterface;
use Zend\Stdlib\DateTime;
use Traversable;


/**
 *
 * @property int $id
 * @property string $title
 * @property string $author
 * @property string $rules
 * @property string $level
 * @property datetime $creationDate
 * @property ArrayCollection $tags
 */

class Listquest extends EntityAbstract implements InputFilterAwareInterface
{
    /**
     * Primary Identifier
     *
     * @var integer
     * @access protected
     */
    protected $id;

    /**
     * Title of each list
     *
     * @var string
     * @access protected
     */
    protected $title;
    
    /**
     * Rules for the quiz
     *
     * @var string
     * @access protected
     */    
    protected $rules;
    
    /**
     * Level of the quiz
     *
     * @var string
     * @access protected
     */
    protected $level;

    /**
     * 
     * @var ZfcUserLL\Entity\User
     * 
     */
    protected $author;
    
    /**
     * 
     * @var datetime
     * @access protected
     * 
     */
    protected $creationDate;
    
    /**
     * @var ArrayCollection of Question\Entity\Question
     */
    protected $questions;
    
    /**
     * ArrayCollection of Question\Entity\Tag
     */
    protected $tags;    
    
    protected $inputFilter;
    
    public function __construct(UserInterface $user = NULL) 
    {
        $this->questions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->setCreationDate(new DateTime());
        
        if ($user){
            $this->author = $user;
        }
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
        return $this;
    }
    
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
    
    public function getLevel()
    {
        return $this->level;
    }
    
    public function setRules($rules)
    {
        $this->rules = $rules;
        return $this;
    }
    
    public function getRules()
    {
        return $this->rules;        
    }
    
    public function setTags($tags)
    {
        foreach ($tags as $tag){
            $this->addTag($tag);
        }
        return $this;
    }
    
    public function getTags()
    {
        return $this->tags;
    }
    
    public function addTag(Tag $tag)
    {
        $tag->addListquest($this);
        $this->tags[] = $tag;
        return $this;
    }
    public function addTags(Collection $tags)
    {
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }
        return $this;
    }
    public function removeTags(Collection $tags)
    {
        foreach ($tags as $tag) {
            $tag->listquests->removeElement($this);
            $this->tags->removeElement($tag);
        }
    }
    
    public function getQuestions()
    {
        return $this->questions;
    }
    
    public function addQuestion(Question $question)
    {
        $this->questions->add($question);
        $question->setListquest($this);
        return $this;
    }
    
    public function addQuestions(Collection $questions)
    {
        foreach ($questions as $question) {
            $this->addQuestion($question);
        }
        return $this;
    }
    
    public function removeQuestions(Collection $questions)
    {
        foreach ($questions as $question) {
            $question->setListquest(null);
            $this->questions->removeElement($question);
        }
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
                'name'     => 'tags',
                'required' => false,
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'questions',
                'required' => false,
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
            
            $inputFilter->add($factory->createInput([
                'name'     => 'level',
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