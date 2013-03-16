<?php
namespace Question\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
    * @var Question\Entity\Listquest
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
     * @var ArrayCollection of Question\Entity\Questionresult
     */
    protected $questionresults;
    
    public function __construct() 
    {
        $this->questionresults = new ArrayCollection();       
    }
    
    public function toArray()
    {
        $questionresults = [];
        foreach ($this->questionresults as $questionresult) {            
            $questionresults[] = $questionresult->toArray();
        }
        
        return [
            'id'        => $this->id,
            'userId'    => $this->user->getId(),
            'startDate'    => $this->startDate,
            'endDate'       => $this->endDate,
            'questionresults' => $questionresults
        ];
    }
    
}