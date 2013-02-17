<?php
namespace Question\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="tags")
 * @property int $id
 * @property string $tag
 * @property ArrayCollection $listquests
 */

class Tag extends EntityAbstract
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
    protected $tag;    
    

    /**
    * 
    * @ORM\ManyToMany(targetEntity="Listquest", inversedBy="tags")
    * 
    * 
    */
    protected $listquests;
    
    public function __construct() 
    {
        $this->listquests = new ArrayCollection();
    }
    
    public function addListquest(Listquest $article)
    {
        $this->listquests[] = $article;
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
    
}