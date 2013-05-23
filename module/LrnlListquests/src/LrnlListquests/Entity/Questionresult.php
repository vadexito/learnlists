<?php
namespace LrnlListquests\Entity;

use LrnlListquests\Entity\Round;
use LrnlListquests\Entity\Question;
use VxoUtils\Entity\EntityAbstract;

class Questionresult extends EntityAbstract
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
     * 
     * @var LrnlListquests\Entity\Round
     * @access protected
     */
    protected $round;    
    

    /**
    * 
    * @var LrnlListquests\Entity\Question
    * @access protected
    * 
    */
    protected $question;
    
    /**
    * 
    * @var string
    * @access protected
    * 
    */
    protected $answerType;
    
    protected $points;
    
    public function toArray()
    {
        $array = [
            'id'            => $this->id,
            'questionId'    => $this->question->id,
            'roundId'       => $this->round->id,
            'answerType'    => $this->answerType,
            'points'        => $this->points,
        ];
        
        return $array;
        
    }
    
    public function setRound(Round $round = NULL)
    {
        $this->round = $round;
        return $this;
    }
    
    public function getRound()
    {
        return $this->round;
    }
    
    public function setPoints($points)
    {
        $this->points = $points;
        return $this;
    }
    
    public function getPoints()
    {
        return $this->points;
    }
    
    public function setQuestion(Question $question = NULL)
    {
        $this->question = $question;
        return $this;
    }
    
    public function getQuestion()
    {
        return $this->question;
    }
    
    public function setAnswerType($answerType)
    {
        $this->answerType = $answerType;
        return $this;
    }
    
    public function getAnswerType()
    {
        return $this->answerType;
    }
}