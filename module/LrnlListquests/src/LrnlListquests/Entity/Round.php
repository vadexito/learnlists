<?php
namespace LrnlListquests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;
use LrnlListquests\Entity\Listquest;
use ZfcUser\Entity\UserInterface;
use Doctrine\Common\Collections\Collection;

class Round extends EntityAbstract
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
     * @var ZfcUserLL\Entity\User
     * 
     */
    protected $user;
    

    /**
    * 
    * @var LrnlListquests\Entity\Listquest
    * 
    */
    protected $listquest;
    
    /**
     * 
     * @ORM\Column(name="start_date",type="datetime")
     * @var datetime
     * @access protected
     * 
     */
    protected $startDate;
    
    /**
     * 
     * @ORM\Column(name="end_date",type="datetime")
     * @var datetime
     * @access protected
     * 
     */
    protected $endDate;
    
    /**
     * @var ArrayCollection of LrnlListquests\Entity\Questionresult
     */
    protected $questionresults;
    
    public function __construct() 
    {
        $this->questionresults = new ArrayCollection();       
    }
    
    public function toArray()
    {
        $roundArray = [
            'id'        => $this->id,
            'userId'    => $this->user->getId(),
            'startDate' => $this->startDate,
            'endDate'   => $this->endDate
        ];
        
        if ($this->questionresults->count() > 0){
            $questionresults = [];
            foreach ($this->questionresults as $questionresult) {            
                $questionresults[] = $questionresult->toArray();
            }
            $roundArray['questionresults'] = $questionresults;
        }
        
        return $roundArray;
    }
    public function getId()
    {
        return $this->id;
    }
    
    public function setUser(UserInterface $user = NULL)
    {
        $this->user = $user;
        return $this;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setListquest(Listquest $listquest = NULL)
    {
        $this->listquest = $listquest;
        return $this;
    }
    
    public function getListquest()
    {
        return $this->listquest;
    }
    
    public function setStartDate(DateTime $startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }
    
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    public function setEndDate(DateTime $endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }
    
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    public function addQuestionresults(Collection $questionresults)
    {
        foreach ($questionresults as $questionresult) {
            $questionresult->setRound($this);
            $this->questionresults->add($questionresult);
        }
    }

    public function removeQuestionresults(Collection $questionresults)
    {
        foreach ($questionresults as $questionresult) {
            $questionresult->setRound(NULL);
            $this->questionresults->removeElement($questionresult);
        }
    }

    public function getQuestionresults()
    {
        return $this->questionresults;
    }
    
}