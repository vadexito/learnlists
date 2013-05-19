<?php

namespace VxoDisqus\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions 
{
    /**
     * @var string
     */
    protected $enabled = true;

    /**
     * @var array
     */
    protected $shortName = '';
    
    
    public function getEnabled()
    {
        return $this->enabled;        
    }
    
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;  
        return $this;
    }
    
    public function getShortName()
    {
        return $this->shortName;        
    }
    
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;  
        return $this;
    }
    
    
}
