<?php
namespace LrnlListquests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
    
    public function toArray()
    {
        $array = [
            'id'            => $this->id,
            'questionId'    => $this->question->id,
            'roundId'       => $this->round->id,
            'answerType'    => $this->answerType,
        ];
        
        return $array;
        
    }
}