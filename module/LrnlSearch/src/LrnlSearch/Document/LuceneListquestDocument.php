<?php

namespace LrnlSearch\Document;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use LrnlListquests\Entity\Listquest;
use LrnlSearch\Traits\LuceneSearchTrait;
use WtRating\Service\RatingService;

class LuceneListquestDocument extends Document implements ListquestDocumentInterface
{
    use LuceneSearchTrait;
    
    protected $_ratingService;
    
    public function __construct(RatingService $ratingService)
    {
        $this->setRatingService($ratingService);
    }
    
    public function createDocumentFromListquest($id,Listquest $list)
    {
        $this->addField(Field::keyword('listId',$this->convertNumToString($list->id)));  
        $this->addField(Field::keyword('questionNb',
            $this->convertNumToString(count($list->questions))));  
        $this->addField(Field::keyword('docId',$this->convertNumToString($id)));  
        $this->addField(Field::unStored('title',$list->title));  
        $this->addField(Field::unStored('description',$list->description));  
        $this->addField(Field::unStored('category',$list->category->getName()));  
        $this->addField(Field::unStored('language',$list->language->getName()));  
        $this->addField(Field::unStored('authorName',$list->author->getUserName()));  
        $this->addField(Field::unStored('authorRole',$list->author->getRoles()[0]->getRoleId()));  
        $this->addField(Field::unStored('level',$list->level->getName()));          
        $this->addField(Field::unIndexed('authorEmail',$list->author->getEmail()));  
        $this->addField(Field::unIndexed('creationDate',$list->creationDate->getTimeStamp()));

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