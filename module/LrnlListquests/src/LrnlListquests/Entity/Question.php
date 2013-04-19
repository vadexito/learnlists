<?php
namespace LrnlListquests\Entity;


use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Collections\ArrayCollection;

use LrnlListquests\Entity\Listquest;
use LrnlListquests\Entity\Questionresult;

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
     * @var ArrayCollection of LrnlListquests\Entity\Questionresult
     */
    protected $questionresults;
    
    /**
    * 
    * @var LrnlListquests\Entity\ListQuest
    */
    protected $listquest;
    
    protected $inputFilter;
    
    public function __construct() 
    {
        $this->questionresults = new ArrayCollection();
    }
    
    public function getQuestionresults()
    {
        return $this->questionresults;
    }
    
    public function addQuestionresult(Questionresult $questionresult)
    {
        $this->questionresults->add($questionresult);
        $questionresult->setListquest($this);
        return $this;
    }
    
    public function addQuestionresults(Collection $questionresults)
    {
        foreach ($questionresults as $questionresult) {
            $this->addQuestionresult($questionresult);
        }
        return $this;
    }
    
    public function removeQuestionresults(Collection $questionresults)
    {
        foreach ($questionresults as $questionresult) {
            $questionresult->setQuestion(null);
            $this->questionresults->removeElement($questionresult);
        }
    }
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
    
    public function getId()
    {
        return $this->id;
    }
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
    public function getText()
    {
        return $this->text;
    }
    public function setTip($tip)
    {
        $this->tip = $tip;
        return $this;
    }
    public function getTip()
    {
        return $this->tip;
    }
    public function setAnswer($answer)
    {
        $this->answer = $answer;
        return $this;
    }
    public function getAnswer()
    {
        return $this->answer;
    }
    public function setListquest(Listquest $listquest = NULL)
    {
        $this->listquest = $listquest;
        return $this;
    }
    public function getListquest()
    {
        return $this->listquest ;
    }
    
}