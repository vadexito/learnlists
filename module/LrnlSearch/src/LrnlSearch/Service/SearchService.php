<?php

namespace LrnlSearch\Service;

use ZendSearch\Lucene\Index;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Exception as LuceneException;
use ZendSearch\Lucene\Search\Query;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive as UTF8NumCaseInsensitiveAnalyser;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;

use LrnlSearch\Document\ListquestDocument;
use LrnlSearch\Traits\LuceneSearchTrait;
use LrnlListquests\Service\ListquestService;
use LrnlListquests\Provider\ProvidesListquestService;

class SearchService
{
    use LuceneSearchTrait;
    use ProvidesListquestService;
    
    protected $_indexPath;
    
    public function __construct($indexPath,ListquestService $listquestService = NULL)
    {
        $this->setIndexPath($indexPath);
        $this->setListquestService($listquestService);
    }
    
    public function getResultsFromQuery($queryData,$sortOptions = NULL)
    {
        $index = Lucene\Lucene::open($this->getIndexPath());
        $query = new Query\Boolean();
        $rangeQuery = new Query\Range(new Index\Term('0','docId'),null,true);
        $query->addSubquery($rangeQuery,true);
        
        $termParams = ['search','authorName','level','language'];
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
                $min = $this->convertNumToString($queryData[$param.'Min']);
                $termMin = new Index\Term($min,$param);
            } 
            if (isset($queryData[$param.'Max']) && $queryData[$param.'Max']){
                $max = $this->convertNumToString($queryData[$param.'Max']);
                $termMax = new Index\Term($max,$param);
            }
            if ($termMin || $termMax){
                $query->addSubquery(new Query\Range($termMin,$termMax,true),true);
            }
        }   
        
        try {
            if ($sortOptions !== NULL){                
                $hits = $index->find($query,$sortOptions['name'],$sortOptions['type'],$sortOptions['direction']);
            } else {
                $hits = $index->find($query);
            }
        }
        catch (LuceneException $ex) {
            $hits = [];
        }

        return $hits;
    }
    
    public function buildIndex()
    {
        $index = Lucene\Lucene::create($this->getIndexPath());
        Analyzer::setDefault(new UTF8NumCaseInsensitiveAnalyser);
        $lists = $this->getListquestService()->fetchAll();
        
        $id =0;
        foreach ($lists as $list) {
            $index->addDocument((new ListquestDocument())->setData($id,$list));
            $id++;
        }
        $index->commit();
        $index->optimize();
    }
    
    //TO DO to be updated
    public function updateIndex()
    {
        $document = getDocument();
        $index = Zend_Search_Lucene::open($this->getIndexPath());

        // find the document based on the indexed document_id field
        $term = new Index\Term($document->id, 'document_id');
        foreach ($index->termDocs($term) as $id)
            $index->delete($id);

        // re-add the document
        $index->addDocument(new ListquestDocument($listquest));
        $index->commit();
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