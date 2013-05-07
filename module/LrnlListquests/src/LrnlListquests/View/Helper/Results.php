<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Exception;
use LrnlListquests\Service\ListquestService;
use LrnlListquests\Provider\ProvidesRoundService;

class Results extends AbstractHelper
{
    protected $_userListquests = NULL;
    
    use ProvidesRoundService;
    
    
    public function __invoke()
    {
        return $this;
    }
    
    public function countUsedListquest()
    {
        return count($listquests = $this->getUserListquests());
        
    }
    
    public function listquestResults()
    {
        $listquests = $this->getRoundService()->fetchListquestsByUser();
        $listquestsLines = [];
        foreach ($listquests as $listquest){
            $listquestsLine = '<p>' .$listquest['title'] 
                . ': rounds completed : ' . $listquest['roundNb']  .'</p>';
            
//            $rounds = [];
//            foreach ($listquest['roundNb'] as $round){
//                $rounds[] = $round['date'].': '.$round['score'];                
//            }
//            $listquestsLine .=$this->getView()->htmlList($rounds,true,false,false);
            $listquestsLines[] = $listquestsLine;
        }
        
        return $listquestsLines;
        
    }
    
    public function setUserListquests($userListquests)
    {
        $this->_userListquests = $userListquests;
    }
    
    public function getUserListquests()
    {
        if ($this->_userListquests === NULL){
            $userListquests = $this->getRoundService()->fetchListquestsByUser();        
            $this->_userListquests = $userListquests;
        }
        return $this->_userListquests;
    }
    
    
}