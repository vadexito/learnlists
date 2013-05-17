<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Service\ListquestService;
use WtRating\Service\RatingService;
use LrnlListquests\Provider\ProvidesListquestService;
use ZendSearch\Lucene\Search\QueryHit;

class ListquestCollection extends AbstractHelper
{
    protected $_lists;
    protected $_ratingService;
    
    use ProvidesListquestService;
    
    public function __invoke($lists)
    {
        $data = []; 
        
        foreach ($lists as $listquest){            
            if ($listquest instanceof QueryHit){
                $listId = (int)$listquest->listId;
            } else {
                $listId = (int)$listquest->id;
            }
            
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
                'urlImage' => $this->getView()->listquestPictureUrl($listDataBase),
                'title' => $listDataBase->getTitle(),
                'description' => $listDataBase->description,
                'category' => $listDataBase->category->getName(),
                'authorEmail' => $listDataBase->author->getEmail(),
                'author' => $listDataBase->author,
                'questionNb' => $listDataBase->questions->count(),
                'level' => $listDataBase->level->getName(),
                'hasLike' => $hasLike,
                'rating' => round($this->getRatingService()
                                 ->getRatingSet($listId)
                                 ->getAmount()),
                'creationDate' => $listDataBase->creationDate,
            ];
        }
        $this->_lists = $data;
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->getView()->partialLoop('listquest_line.phtml',$this->_lists);
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