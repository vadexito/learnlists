<?php

namespace LrnlSearch\Service;

use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Exception as LuceneException;
use ZendSearch\Lucene\Search\Query;

use LrnlSearch\Exception\ServiceException;
use LrnlSearch\Document\ListquestDocument;

class SearchService
{
    protected $_indexPath;
    
    public function __construct($indexPath)
    {
        $this->setIndexPath($indexPath);
    }
    
    public function getListquestsFromQuery($queryData)
    {
        $index = Lucene\Lucene::open($this->getIndexPath());
        $query = new Query\Boolean();
        $rangeQuery = new Query\Range(new Index\Term('0','docId'),null,true);
        $query->addSubquery($rangeQuery,true);
        
        $termParams = ['search','authorName','level'];
        foreach ($termParams as $param){
            if (isset($queryData[$param]) && $queryData[$param]){
                if (!is_array($queryData[$param])){
                    $termQuery = new Query\Term(new Index\Term($queryData[$param]));
                } else {
                    $termQuery = new Query\MultiTerm();
                    foreach ($queryData[$param] as $value) {
                        $termQuery->addTerm(new Index\Term($value));
                    }
                }
                $query->addSubquery($termQuery,true);
            } 
        }
        
        $rangeParams = ['questionNb'];
        foreach ($rangeParams as $param){
            $termMin = NULL;
            $termMax = NULL;
            if (isset($queryData[$param.'Min']) && $queryData[$param.'Min']){
                $termMin = new Index\Term($queryData[$param.'Min']);
            } 
            if (isset($queryData[$param.'Max']) && $queryData[$param.'Max']){
                $termMax = new Index\Term($queryData[$param.'Max']);
            }
            if ($termMin || $termMax){
                $query->addSubquery(new Query\Range($termMin,$termMax,true),true);
            }
        }   
        
        try {
            $hits = $index->find($query);
        }
        catch (LuceneException $ex) {
            $hits = [];
        }

        return $hits;
    }
    
    public function setIndexPath($indexPath)
    {
        $this->_indexPath = $indexPath;
        return $this;
    }
    
    public function getIndexPath()
    {
        return $this->_indexPath;
    }

    
}