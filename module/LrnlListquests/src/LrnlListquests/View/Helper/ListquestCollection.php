<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use VxoReview\Provider\ProvidesReviewService;
use LrnlListquests\Provider\ProvidesListquestService;
use ZendSearch\Lucene\Search\QueryHit;
use LrnlListquests\Entity\ListquestInterface;

class ListquestCollection extends AbstractHelper
{
    protected $_lists;
    protected $_ratingService;
    
    use ProvidesListquestService;
    use ProvidesReviewService;
    
    public function __invoke($lists = null)
    {
        if ($lists === null){
            return $this;
        }
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
            $data[] = $this->initDataLine($listDataBase);
        }
        $this->_lists = $data;
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->getView()->partialLoop('listquest_line.phtml',$this->_lists);
    }
    
    public function initDataLine(ListquestInterface $listDataBase)
    {
        $listId = $listDataBase->id;
        return [
            'id' => $listId,
            'urlImage' => $this->getView()->listquestPictureUrl($listDataBase),
            'title' => $listDataBase->getTitle(),
            'description' => $listDataBase->description,
            'category' => $listDataBase->category,
            'authorEmail' => $listDataBase->author->getEmail(),
            'author' => $listDataBase->author,
            'questionNb' => $listDataBase->questions->count(),
            'level' => $listDataBase->level,
            'creationDate' => $listDataBase->creationDate,
            'reviewNb' => $this->getReviewService()->getReviewCount($listId),
            'rating' => $this->getReviewService()->getRating($listId),
            'ratingMax' => $this->getReviewService()->getRatingMax($listId),
        ];
        
    }
    
    public function miniLine(ListquestInterface $list)
    {
        $data = $this->initDataLine($list);
        return $this->getView()->partial('lrnl-listquests/listquest/home/listquest_line_mini.phtml',$data);
    }
    
}