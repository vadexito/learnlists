<?php
namespace Question\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="questions")
 * @property int $id
 * @property string $text
 * @property string $answer
 * @property string $tip
 */

class Question extends EntityAbstract
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
     *
     * @ORM\Column(type="string")
     * @var string
     * @access protected
     */
    protected $text;
    /**
     *
     * @ORM\Column(type="string")
     * @var string
     * @access protected
     */
    protected $answer;
    /**
     *
     * @ORM\Column(type="string")
     * @var string
     * @access protected
     */
    protected $tip;

    /**
    * 
    * @ORM\ManyToOne(targetEntity="Listquest", inversedBy="questions")
    * @ORM\JoinColumn(name="listquests_id", referencedColumnName="id")
    * 
    */
    protected $listquest;
    
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
    
}