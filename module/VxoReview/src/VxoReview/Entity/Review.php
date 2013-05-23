<?php
namespace VxoReview\Entity;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface; 
use ZfcUser\Entity\UserInterface as User;
use DateTime;
use VxoUtils\Entity\EntityAbstract;


class Review extends EntityAbstract implements 
    InputFilterAwareInterface,
    ReviewInterface
{
    
    /**
     * Primary Identifier
     *
     * @var integer
     * @access protected
     */
    protected $id;

    /**
     * text of the review
     *
     * @var string
     * @access protected
     */
    protected $text;
    /**
     * author of the review
     *
     * @access protected
     */
    protected $author; 
    
    /**
     * Description
     *
     * @var string
     * @access protected
     */
    protected $reviewedItem;
    
    /**
     * Rating of the item
     *
     * @var integer
     * @access protected
     */
    protected $rating;
    
    protected $creationDate;
    
    protected $inputFilter;
    
    public function getId()
    {
        return $this->id;
    }
   

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor(User $author)
    {
        $this->author = $author;
        return $this;
    }

    public function getReviewedItem()
    {
        return $this->reviewedItem;
    }

    public function setReviewedItem($reviewedItem)
    {
        $this->reviewedItem = $reviewedItem;
        return $this;
    }

    public function getRating()
    {
        return $this->rating;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
        return $this;
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
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function __toString()
    {
        return $this->getText();
    }
    
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput([
                'name'     => 'id',
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'reviewedItem',
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
                            'min'      => 0,
                            'max'      => 250,
                        ],
                    ],
                ],
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'rating',
                'required' => false,
                'filters'  => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    ['name'    => 'Int'],
                ],
            ]));
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}