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
use LrnlSearch\Form\FiltersForm;
use LrnlSearch\Exception\SearchException;
use LrnlListquests\Entity\Listquest;

use WtRating\Service\RatingService;
use Traversable;

class SearchService
{
    use LuceneSearchTrait;
    use ProvidesListquestService;
    
    protected $_indexPath;
    protected $_ratingService;
    protected $_filterConfig;
    
    public function __construct($indexPath,
            ListquestService $listquestService = NULL,
            RatingService $ratingService = NULL, Traversable $filterConfig = NULL)
    {
        $this->setIndexPath($indexPath);
        $this->setListquestService($listquestService);
        $this->setRatingService($ratingService);
        $this->setFilterConfig($filterConfig);
    }
    
    public function getResultsFromQuery($queryData,$sortOptions = NULL)
    {
        $index = Lucene\Lucene::open($this->getIndexPath());
        $query = new Query\Boolean();
        
        //add all, so that if no query it return everything
        $allQuery = new Query\Range(new Index\Term('0','docId'),null,true);
        $query->addSubquery($allQuery,true);
        
        foreach ($queryData as $filter => $values){
            if ($filter === 'search' && is_string($values)){
                if (!is_string($values)){
                    throw new SearchException('You must provide a string for each search.');
                }       
                $termQuery = new Query\Term(new Index\Term($values));
                $query->addSubquery($termQuery,true);
            }
            
            $filterConfig = $this->getFilterConfig()->get($filter);
            if ($filterConfig !== NULL){
                switch ($filterConfig['type']){
                    case FiltersForm::$CHECKBOX :
                        if (!is_array($values)){
                            throw new SearchException('You must provide an array for each checkbox element in your url.');
                        }                        
                        $termQuery = new Query\MultiTerm();
                        foreach ($values as $value){
                            $termQuery->addTerm(new Index\Term($value,$filter));
                        }
                        $query->addSubquery($termQuery,true);                        
                        break;
                    case FiltersForm::$RANGE :
                        if (!is_array($values) || 
                                (!isset($values['min']) || !isset($values['max']))){
                            throw new SearchException('You must provide an array with min and max value');
                        }
                        $min = $this->convertNumToString($values['min']);
                        $max = $this->convertNumToString($values['max']);                        
                        $termMin = new Index\Term($min,$filter);
                        $termMax = new Index\Term($max,$filter);
                        $query->addSubquery(new Query\Range($termMin,$termMax,true),true);                        
                        break;
                    case FiltersForm::$SEARCH :
                        if (!is_array($values) && count($values) === 1){
                            throw new SearchException('You must provide an array for each search.');
                        }
                        foreach ($values as $value){
                            //no search if no value
                            if ($value){
                                $termQuery = new Query\Term(new Index\Term($value));
                                $query->addSubquery($termQuery,true);
                            }
                        }
                        break;
                    default:
                }
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
            $newDocument = new ListquestDocument($this->getRatingService());
            $index->addDocument($newDocument->setData($id,$list));
            $id++;
        }
        $index->commit();
        $index->optimize();
    }
    
    public function updateIndex(Listquest $listquest)
    {
        $index = Lucene\Lucene::open($this->getIndexPath());
        
        $hit = $index->find('listquestId:'.$this->convertNumToString($listquest->id));
        $docId = $index->count()+1;
        if ($hit){
            $docId = $hit->docId;
            $index->delete($hit->id);
        }
        
        $newDocument = new ListquestDocument($this->getRatingService());
        $newDocument->setData((int)$docId,$listquest);
        
        $index->addDocument($newDocument);
        $index->commit();
        $index->optimize();
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
    
    public function getRatingService()
    {
        return $this->_ratingService;
    }
    
    public function setRatingService(RatingService $service)
    {
        $this->_ratingService = $service;
        return $this;
    }
    
    public function getFilterConfig()
    {
        return $this->_filterConfig;
    }
    
    public function setFilterConfig(Traversable $filterConfig)
    {
        $this->_filterConfig = $filterConfig;
        return $this;
    }
}