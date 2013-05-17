<?php

namespace LrnlSearch\Document;

use Elastica\Document;
use ZendSearch\Lucene\Document\Field;
use LrnlListquests\Entity\Listquest;
use LrnlSearch\Traits\LuceneSearchTrait;
use WtRating\Service\RatingService;

class ElasticaListquestDocument extends Document implements ListquestDocumentInterface
{
    use LuceneSearchTrait;
    
    protected $_ratingService;
    
    public function __construct(RatingService $ratingService)
    {
        $this->setRatingService($ratingService);
    }
    
    public function createDocumentFromListquest($id,Listquest $list)
    {
        $tags = '';
        foreach ($list->tags as $tag){
            $tags .= $tag->tag;
        }
        $questions = '';
        foreach ($list->questions as $question){
            $questions .= $question->text.' '
                    .$question->answer.' '
                    .$question->comment;
        }
        
        $dataList = array(
            'id'      => (int)$id,
            'listId'  => (int)$list->id,
            'questionNb'  => (int)count($list->questions),
            'rating'  => (int)round($this->getRatingService()->getRatingSet($list->id)->getAmount()),
            'authorName' => $list->author->getUserName(),
            'author' => $list->author->getRoles()[0]->getRoleId(),
            'identity'    => array(
                'name'      => $list->author->getUserName(),
                'role'  => $list->author->getRoles()[0]->getRoleId(),
                'email'  => $list->author->getEmail(),
            ),
            'title'     => $list->title,
            'category'  => $list->category->getName(),
            'description'=> $list->description,
            'language'=> $list->language->getName(),
            'level'=> $list->level->getName(),
            'creationDate' => $list->creationDate->getTimeStamp(),
            'tags'=> $tags,
            'questions'=> $questions,
            '_boost'  => 1.0
        );
        
        $this->setId($id)->setData($dataList); 
        
        return $this;
    }
    
    public function getRatingService()
    {
        return $this->_ratingService;
    }
    
    public function setRatingService(RatingService $service)
    {
        $this->_ratingService = $service;
        return $this;
    }
    
    
}