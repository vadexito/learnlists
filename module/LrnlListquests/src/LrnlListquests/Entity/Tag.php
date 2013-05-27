<?php
namespace LrnlListquests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;   
use VxoUtils\Entity\EntityAbstract;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="tags")
 * @property int $id
 * @property string $tag
 * @property ArrayCollection $listquests
 */

class Tag extends EntityAbstract implements InputFilterAwareInterface
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
    
    protected $inputFilter;
    
    public function __construct() 
    {
        $this->listquests = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function addListquest(Listquest $listquest)
    {
        $this->listquests[] = $listquest;
    }
    
    public function addListquests(Collection $listquests)
    {
        foreach ($listquests as $listquest) {            
            $this->addListquest($listquest);
        }
    }

    public function removeListquests(Collection $listquests)
    {
        foreach ($listquests as $listquest) {
            $this->listquests->removeElement($listquest);
        }
    }

    public function getListquests()
    {
        return $this->listquests;
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
    
    public function setTag($tag)
    {
        $this->tag = $tag;
        return $this;
    }
    
    public function getTag()
    {
        return $this->tag;
    }
    
    public function __toString()
    {
        return $this->tag;
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
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]));
            
            $inputFilter->add($factory->createInput([
                'name'     => 'tag',
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
                            'max'      => 20,
                        ],
                    ],
                ],
            ]));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}