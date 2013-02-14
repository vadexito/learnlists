<?php
namespace Question\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="listquests")
 * @property int $id
 * @property string $title * 
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
     * 
     * @ORM\OneToMany(targetEntity="Question", mappedBy="listquest")
     * 
     */
    protected $questions;

    public function __construct() 
    {
        $this->questions = new ArrayCollection();
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
            'questions' => $questions,
        ];
    }

}