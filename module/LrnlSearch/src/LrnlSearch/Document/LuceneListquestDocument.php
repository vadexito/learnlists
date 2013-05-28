<?php

namespace LrnlSearch\Document;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Document\Field;
use LrnlListquests\Entity\Listquest;
use LrnlSearch\Traits\LuceneSearchTrait;


class LuceneListquestDocument extends Document implements ListquestDocumentInterface
{
    use LuceneSearchTrait;
    
    protected $_entity;
    
    public function createDocumentFromListquest($id,Listquest $list)
    {
        $this->_entity = $list;
        
        $this->addField(Field::keyword('listId',$this->convertNumToString($list->id)));  
        $this->addField(Field::keyword('questionNb',
            $this->convertNumToString(count($list->questions))));  
        $this->addField(Field::keyword('docId',$this->convertNumToString($id)));  
        $this->addField(Field::unStored('title',$list->title));  
        $this->addEntityProperty('description','unstored');
        $this->addEntityProperty(['level','id'],'keyword');
        $this->addEntityProperty(['category','id'],'keyword');
        $this->addEntityProperty(['language','id'],'keyword');
        $this->addField(Field::unStored('author',$list->author->getRoles()[0]->getRoleId())); 
        $this->addField(Field::unStored('authorName',$list->author->getUserName()));
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
    
    public function addEntityProperty($properties,$type)
    {
        $property = $properties;
        if (is_array($properties)){
            $subProperty = $property[1];
            $property = $property[0];
        }
        
        $entity = $this->_entity;
        if (!property_exists($entity,$property)){
           return false;
        }
        $getter = 'get'.ucfirst(strtolower($property));
        $value = $this->_entity->$getter();
        if (!$value){
            return false;
        }
        
        if (is_array($properties)){
            $value = $value->$subProperty;
        } else {
            $value = (string)$value;
        }
        if ($type === 'keyword'){
            $value = $this->convertNumToString($value);
        }
        $this->addField(Field::$type($property,$value));
        return true;
    }
}