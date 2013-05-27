<?php
namespace LrnlListquests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 
use Zend\InputFilter\CollectionInputFilter;

use LrnlListquests\Entity\Round;
use LrnlListquests\Entity\Tag;
use LrnlListquests\Entity\Question;
use LrnlListquests\Entity\Level;
use LrnlListquests\Entity\Category;
use LrnlListquests\Entity\Language;
use VxoUtils\Entity\EntityAbstract;

use DateTime;


class Listquest extends EntityAbstract implements 
    InputFilterAwareInterface,
    ListquestInterface
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
     * description of the list
     *
     * @var string
     * @access protected
     */
    protected $description;
    
    /**
     * $category of the list
     *
     * @var string
     * @access protected
     */
    protected $category;
    
    /**
     * language of the list
     *
     * @var string
     * @access protected
     */
    protected $language;
    
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
     * @var LrnlUser\Entity\User
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
     * @var ArrayCollection of LrnlListquests\Entity\Question
     */
    protected $questions;
    
    /**
     * @var ArrayCollection of LrnlListquests\Entity\Round
     */
    protected $rounds;
    
    /**
     * ArrayCollection of LrnlListquests\Entity\Tag
     */
    protected $tags;    
    
    protected $pictureId;
    
    protected $inputFilter;
    
    public function __construct() 
    {
        $this->questions = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->rounds = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setCreationDate(DateTime $creationDate)
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
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setCategory(Category $category)
    {
        $this->category = $category;
        return $this;
    }
    
    public function getCategory()
    {
        return $this->category;
    }
    
    public function setAuthor($author)
    {
        $this->author = $author;
        return $this;
    }
    
    public function getAuthor()
    {
        return $this->author;
    }
    public function setLanguage(Language $language)
    {
        $this->language = $language;
        return $this;
    }
    
    public function getLanguage()
    {
        return $this->language;
    }
    
    public function setLevel(Level $level)
    {
        $this->level = $level;
        return $this;
    }
    
    public function getLevel()
    {
        return $this->level;
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
    
    public function getRounds()
    {
        return $this->rounds;
    }
    
    public function addRound(Round $round)
    {
        $this->rounds->add($round);
        $round->setListquest($this);
        return $this;
    }
    
    public function addRounds(Collection $rounds)
    {
        foreach ($rounds as $round) {
            $this->addRound($round);
        }
        return $this;
    }
    
    public function removeRounds(Collection $rounds)
    {
        foreach ($rounds as $round) {
            $round->setListquest(null);
            $this->rounds->removeElement($round);
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
                            'min'      => 2,
                            'max'      => 35,
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
                'name'     => 'category',
                'required' => false,
            ]));
            $inputFilter->add($factory->createInput([
                'name'     => 'language',
                'required' => false,
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'level',
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
            
            $questionsCollectionFilter = new CollectionInputFilter();
            $inputFilter->add($questionsCollectionFilter,'questions');
            
            $tagsCollectionFilter = new CollectionInputFilter();
            $inputFilter->add($tagsCollectionFilter,'tags');
            
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}