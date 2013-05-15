<?php 

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;


class Site extends AbstractHelper
{
    protected $_siteName = 'Learnlists';
    
    public function __invoke()
    {
        return $this;
    }
    
    public function __toString()
    {
        return $this->siteName();
    }
    
    public function siteName()
    {
        return $this->_siteName;
    }
}