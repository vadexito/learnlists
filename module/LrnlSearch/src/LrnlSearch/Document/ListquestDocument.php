<?php

namespace LrnlSearch\Document;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use LrnlListquests\Entity\Listquest;
use LrnlSearch\Traits\LuceneSearchTrait;

class ListquestDocument extends Document
{
    use LuceneSearchTrait;
    
    public function setData($id,Listquest $list)
    {
        $this->addField(Field::keyword('listId',$this->convertNumToString($list->id)));  
        $this->addField(Field::keyword('questionNb',
                $this->convertNumToString(count($list->questions))));  
        $this->addField(Field::keyword('docId',$this->convertNumToString($id)));  
        $this->addField(Field::Text('title',$list->title));  
        $this->addField(Field::Text('description',$list->description));  
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
    
    
}