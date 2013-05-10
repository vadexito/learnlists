<?php

namespace LrnlSearch\Service;

use ZendSearch\Lucene\Index;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Exception as LuceneException;
use ZendSearch\Lucene\Search\Query;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive as UTF8NumCaseInsensitiveAnalyser;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use Zend\Stdlib\Parameters;

use LrnlSearch\Document\LuceneListquestDocument;
use LrnlSearch\Traits\LuceneSearchTrait;
use LrnlListquests\Service\ListquestService;
use LrnlListquests\Provider\ProvidesListquestService;
use LrnlSearch\Form\FiltersForm;
use LrnlSearch\Exception\SearchException;
use LrnlListquests\Entity\Listquest;

use WtRating\Service\RatingService;
use Traversable;

class LuceneSearchService implements SearchServiceInterface
{
    use LuceneSearchTrait;
    use ProvidesListquestService;
    
    protected $_indexPath;
    protected $_ratingService;
    protected $_filterConfig;
    
    public function __construct($indexPath,
            ListquestService $listquestService = NULL,
            Traversable $filterConfig = NULL)
    {
        $this->setIndexPath($indexPath);
        $this->setListquestService($listquestService);
        $this->setFilterConfig($filterConfig);
    }
    
    /**
     * 
     * @param type $queryData containing all the parameter for a query to 
     * lucene index
     * @param type $sortOptions for sorting options
     * @return array of hits (lucene hit)
     * @throws SearchException
     */
    public function getResultsFromQuery(Parameters $queryData,$sortOptions = NULL)
    {
        $index = Lucene\Lucene::open($this->getIndexPath());
        $query = new Query\Boolean();
        $isNotNullValue = false;
        
        //add all, so that if no query it return everything
        if ($queryData->count() ===0){
            $allQuery = new Query\Range(new Index\Term('0','docId'),null,true);
            $query->addSubquery($allQuery,true);
        } else {
            foreach ($queryData as $filter => $values){
                if (!$values){
                    continue;
                }
                $isNotNullValue = true;
                //if it is the main search
                if ($filter === 'search' && $values){                
                    $values = explode(' ',$values);
                    $query->addSubquery($this->getQueryForTerms($values,NULL,true),true);
                }
                if ($filter === 'category' && $values){
                    $query->addSubquery($this->getQueryForTerms($values,$filter),true);
                }

                //if it is a filter from the side
                $filterConfig = $this->getFilterConfig()->get($filter);
                if ($filterConfig !== NULL){
                    switch ($filterConfig['type']){
                        case FiltersForm::$CHECKBOX :                       
                            $query->addSubquery($this->getQueryForTerms($values,$filter),true);                        
                            break;
                        case FiltersForm::$RANGE :
                            $query->addSubquery($this->getQueryForRange($values,$filter),true);     
                            break;
                        case FiltersForm::$SEARCH :
                            $query->addSubquery($this->getQueryForTerms($values),true);
                            break;
                        default:
                    }
                }
            }
        }  
        
        if (!$isNotNullValue){
            $allQuery = new Query\Range(new Index\Term('0','docId'),null,true);
            $query->addSubquery($allQuery,true);
        }
        //sort results and perform search
        $hits = [];
        try {
            if ($sortOptions !== NULL){                
                $hits = $index->find($query,$sortOptions['name'],$sortOptions['type'],$sortOptions['direction']);
            } else {
                $hits = $index->find($query);
            }
        }
        catch (LuceneException $ex) {
        }

        return $hits;
    }
    
    public function getCountNumberFromQuery(Parameters $queryData)
    {
        return count($this->getResultsFromQuery($queryData));
    }
    
    public function getFacet($facet,Parameters $queryData,Array $defaultValues)
    {
        $facetValues = [];
        foreach ($defaultValues as $value){                
            $filteredQuery = clone $queryData; 
            $filteredQuery->set($facet,[$value]);                
            $hitNb = $this->getCountNumberFromQuery($filteredQuery);
            $facetValues[] = [
                'term' => $value,
                'count' => $hitNb,
            ];
        }
        
        return $facetValues;
    }
    
    public function buildIndex()
    {
        $index = Lucene\Lucene::create($this->getIndexPath());
        Analyzer::setDefault(new UTF8NumCaseInsensitiveAnalyser);
        $lists = $this->getListquestService()->fetchAll();
        
        $id =0;
        foreach ($lists as $list) {
            $newDocument = $this->getNewListquestDocument();
            $index->addDocument($newDocument->createDocumentFromListquest($id,$list));
            $id++;
        }
        $index->commit();
    }
    
    public function updateIndex(Listquest $listquest)
    {
        $index = Lucene\Lucene::open($this->getIndexPath());
        
        $hit = $index->find('listId:'.$this->convertNumToString($listquest->id));
        $docId = $index->count()+1;
        if ($hit){
            $hit = $hit[0];
            $docId = $hit->docId;
            $index->delete($hit->id);
        }
        
        $newDocument = $this->getNewListquestDocument();
        $newDocument->createDocumentFromListquest((int)$docId,$listquest);
        
        $index->addDocument($newDocument);
        $index->commit();
    }
    
    public function deleteFromIndex($listquestId)
    {
        $index = Lucene\Lucene::open($this->getIndexPath());        
        $hit = $index->find('listquestId:'.$this->convertNumToString($listquestId));
        
        if ($hit){            
            $index->delete($hit->id);
        }
        
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
    
    public function getNewListquestDocument()
    {
        return new LuceneListquestDocument($this->getRatingService());        
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
    
    
    protected function getQueryForTerms($values,$filter = NULL,$operator = NULL)
    {
        if (!is_array($values) && !is_string($values)){
            throw new SearchException('You must provide an array or a string for this element in the url.');
        }
        if (is_string($values)){
            $values = [$values];
        }
        
        $query = new Query\MultiTerm();
        foreach ($values as $value){
            $term = new Index\Term(strtolower($value),$filter);
            $query->addTerm($term,$operator);
        }
        
        return $query;
    }
    
    protected function getQueryForRange(Array $values,$filter = NULL,$inbound = true)
    {
        if (!is_array($values) || 
            (!isset($values['min']) || !isset($values['max']))){
        throw new SearchException('You must provide an array with min and max value');
        }
        
        $min = $this->convertNumToString($values['min']);
        $max = $this->convertNumToString($values['max']);                        
        $termMin = new Index\Term($min,$filter);
        $termMax = new Index\Term($max,$filter);
        $query = new Query\Range($termMin,$termMax,$inbound);
        
        return $query;
    }
}