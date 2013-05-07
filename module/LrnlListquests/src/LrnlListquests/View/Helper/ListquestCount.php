<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Provider\ProvidesListquestService;


class ListquestCount extends AbstractHelper
{
    use ProvidesListquestService;
    
    protected $_count;
    
    public function __invoke()
    {
        return $this->getListquestService()->getCount();
    }
}