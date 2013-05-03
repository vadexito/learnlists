<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Service\ListquestService;
use WtRating\Service\RatingService;
use DateTime;
use ZendSearch\Lucene\Exception\InvalidArgumentException;

class ListquestCollection extends AbstractHelper
{
    protected $_lists;
    protected $_listquestService;
    protected $_ratingService;
    
    public function __invoke($lists)
    {
        $data = [];
        
        foreach ($lists as $list){
            
            $listId = (int)$list->listId;
            $listDataBase = $this->getListquestService()->fetchById($listId);
            if (!$listDataBase){
                continue;
            }
            $hasLike = false;
            $user = $this->getView()->zfcUserIdentity();
            if ($user && $this->getRatingService()->getMapper()->hasRated(
                    $user->getId(),$listId)) {
                
                $hasLike = true;
            }
            
            $data[] = [
                'id' => $listId,
                'title' => $listDataBase->getTitle(),
                'description' => $listDataBase->description,
                'category' => $listDataBase->category,
                'authorEmail' => $listDataBase->author->getEmail(),
                'author' => $listDataBase->author,
                'questionNb' => $listDataBase->questions->count(),
                'level' => $listDataBase->level,
                'hasLike' => $hasLike,
                'rating' => round($this->getRatingService()
                                 ->getRatingSet($listId)
                                 ->getAmount()),
                'creationDate' => $listDataBase->creationDate,
            ];
        }
        $this->_lists = $data;
        
        return $this->render();
    }
    
    public function render()
    {
        return $this->getView()->partialLoop('listquest_line.phtml',$this->_lists);
    }
    
    public function setListquestService(ListquestService $service)
    {
        $this->_listquestService = $service;
        return $this;
    }
    
    public function getListquestService()
    {
        return $this->_listquestService;
    }
    
    public function setRatingService(RatingService $service)
    {
        $this->_ratingService = $service;
        return $this;
    }
    
    public function getRatingService()
    {
        return $this->_ratingService;
    }
}