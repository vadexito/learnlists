<?php 

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Session\Container;

class Language extends AbstractHelper
{
    public function __invoke()
    {
        $session = new Container('learnlists_locale');
        
        switch($session->locale){
            case 'de_DE' :
                return $this->getView()->translate('German');
            case 'fr_FR' :
                return $this->getView()->translate('French');
            case 'en_US' :
                return $this->getView()->translate('English');
            default:
                return '';
        }
    }
}