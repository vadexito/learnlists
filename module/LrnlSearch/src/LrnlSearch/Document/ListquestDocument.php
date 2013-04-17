<?php

namespace LrnlSearch\Document;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use LrnlListquests\Entity\Listquest;
use LrnlSearch\Traits\LuceneSearchTrait;
use WtRating\Service\RatingService;

class ListquestDocument extends Document
{
    use LuceneSearchTrait;
    
    protected $_ratingService;
    
    public function __construct(RatingService $ratingService)
    {
        $this->setRatingService($ratingService);
    }
    
    public function setData($id,Listquest $list)
    {
        $this->addField(Field::keyword('listId',$this->convertNumToString($list->id)));  
        $this->addField(Field::keyword('questionNb',
            $this->convertNumToString(count($list->questions))));  
        $this->addField(Field::keyword('rating',
            $this->convertNumToString(round($this->getRatingService()->getRatingSet($list->id)->getAmount()))));
        $this->addField(Field::keyword('docId',$this->convertNumToString($id)));  
        $this->addField(Field::Text('title',$list->title));  
        $this->addField(Field::Text('description',$list->description));  
        $this->addField(Field::Text('category',$list->category));  
        $this->addField(Field::Text('language',$list->language));  
        $this->addField(Field::Text('authorName',$list->author->getUserName()));  
        $this->addField(Field::Text('authorRole',$list->author->getRoles()[0]->getRoleId()));  
        $this->addField(Field::Text('level',$list->level));          
        $this->addField(Field::unIndexed('authorEmail',$list->author->getEmail()));  
        $this->addField(Field::unIndexed('creationDate',$list->creationDate->getTimeStamp()));  
        $this->addField(Field::unStored('rules',$list->rules)); 

        $tags = '';
        foreach ($list->tags as $tag){
            $tags .= $tag->tag;
        }
        $questions = '';
        foreach ($list->questions as $question){
            $questions .= $question->text.' '
                    .$question->answer.' '
                    .$question->tip;
        }

        $this->addField(Field::Text('tags',$tags));
        $this->addField(Field::Text('questions',$questions));
        
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