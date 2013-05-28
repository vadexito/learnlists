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
use LrnlSearch\Form\FiltersForm;
use LrnlSearch\Exception\SearchException;
use LrnlListquests\Entity\Listquest;


use Traversable;

class LuceneSearchService implements SearchServiceInterface
{
    use LuceneSearchTrait;
    
    protected $_indexPath;
    protected $_filterConfig;
    protected $_index = NULL;
    
    public function __construct($indexPath, Traversable $filterConfig = NULL)
    {
        $this->setIndexPath($indexPath);
        $this->setFilterConfig($filterConfig);
    }
    
    /**
     * 
     * @param type $queryData containing all the parameter for a query to 
     * lucene index
     * @param type $sortOptions for sorting options
     * @return array of hits (lucene hit) or true if all the results are to be given
     * @throws SearchException
     */
    public function getResultsFromQuery(Parameters $queryData,$sortOptions = NULL)
    {
        $index = $this->getIndex();
        
        if ($this->isEmptyQuery($queryData)){
            return 'empty_query';            
        } else {
            $query = $this->getQueryFromArray($queryData);        
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
        $index = $this->getIndex();
        try {
            $query = $this->getQueryFromArray($queryData);
            $optimalQuery = $query->rewrite($index)->optimize($index);
            $optimalQuery->execute($index);        
        }
        catch (LuceneException $ex) {
        }
        
        return count($optimalQuery->matchedDocs());
    }
    
    public function getQueryFromArray(Parameters $queryData)
    {
        if ($this->isEmptyQuery($queryData)){
            throw new SearchException('You must provide non null query data');
        } 
        
        $query = new Query\Boolean();
        foreach ($queryData as $filter => $values){
            if (!$values){
                continue;
            }
            //if it is the main search
            if ($filter === 'search' && $values){                
                $values = explode(' ',$values);
                $this->addQueryForTerms($query,$values,NULL,true);
            }
            if ($filter === 'category' && $values){
                $this->addQueryForTerms($query,$values,$filter);
            }

            //if it is a filter from the side
            $filterForm = $this->getFilterConfig();
            $type = NULL;            
            foreach ($filterForm as $filterElement){
                if ($filterElement['name'] == $filter){
                    $type = $filterElement['options']['filterType'];
                }
            }

            switch ($type){
                case FiltersForm::$CHECKBOX_FACET_SEARCH :                       
                case FiltersForm::$CHECKBOX_FACET_SELECT :
                    $this->addQueryForTerms($query,$values,$filter);                        
                    break;
                case FiltersForm::$RANGE :
                    $query->addSubquery($this->getQueryForRange($values,$filter),true);     
                    break;
                case FiltersForm::$SEARCH :
                    $this->addQueryForTerms($query,$values);        
                    break;
                default:
            }
            
        }
        return $query;
    }
    
    public function isEmptyQuery(Parameters $queryData){
        
        if ($queryData->count() === 0 
                || count($queryData->toArray()) === 0){
            return true;
        } else {
            $query = $queryData->toArray();
            foreach ($query as $key => $value){
                if (empty($value)){
                    unset($query[$key]);
                }
            }
            if (empty($query)){
                return true;
            }
        }
        
        return false;
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
    
    public function buildIndex($lists)
    {
        $index = Lucene\Lucene::create($this->getIndexPath());
        Analyzer::setDefault(new UTF8NumCaseInsensitiveAnalyser);
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
        $index = $this->getIndex();
        
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
        return new LuceneListquestDocument();        
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
    
    protected function prepareValueForSearch($value)
    {
        $pattern = '/^(\d)*-(.*)/';
        $value =(string)strtolower(trim($value)); 
        if (preg_match($pattern, $value)){
            $number = (int)preg_replace($pattern,'$1',$value);
            $value = $this->convertNumToString($number);
        } 
        return $value;
    }
    protected function addQueryForTerms($query,$values,$filter = NULL,$operator = NULL)
    {
        if (!is_array($values) && !is_string($values)){
            throw new SearchException('You must provide an array or a string for this element in the url.');
        }
        if (is_string($values)){
            $values = [$values];
        }
        
        $newQuery = new Query\Boolean();
        foreach ($values as $value){
            $value = $this->prepareValueForSearch($value);
            $words = explode(' ',$value);
            if (count($words) > 1){
                $newQuery->addSubquery(new Query\Phrase($words));
            } else {
                $term = new Index\Term($value,$filter);
                $newQuery->addSubquery(new Query\Term($term));
            }
        }
        
        $query->addSubquery($newQuery,true);   
        return true;
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
    
    public function getIndex()
    {
        if ($this->_index === NULL){
            $this->_index = Lucene\Lucene::open($this->getIndexPath());
        }
        return $this->_index;
    }
    
    public function setIndex($index)
    {
        $this->_index = $index;
        return $this;
    }
}