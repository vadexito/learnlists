<?php

namespace LrnlSearch\Service;

use ZendSearch\Lucene\Index;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Exception as LuceneException;
use ZendSearch\Lucene\Search\Query;


use LrnlSearch\Document\ElasticaListquestDocument;
use LrnlListquests\Service\ListquestService;
use LrnlListquests\Provider\ProvidesListquestService;
use LrnlSearch\Form\FiltersForm;
use LrnlSearch\Exception\SearchException;
use LrnlListquests\Entity\Listquest;

use WtRating\Service\RatingService;
use Traversable;

use Elastica\Client;

class ElasticaSearchService implements SearchServiceInterface
{
    use ProvidesListquestService;
    
    protected $_indexPath;
    protected $_ratingService;
    protected $_filterConfig;
    protected $client;
    protected $index = NULL;
    
    public function __construct($indexPath,
            ListquestService $listquestService = NULL,
            Traversable $filterConfig = NULL)
    {
        
        $client = new Client([
            'host' => 'localhost',
            'port' => 9200
        ]);
        
        $this->setClient($client);
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
    public function getResultsFromQuery($queryData,$sortOptions = NULL)
    {
        
        // Define a Query. We want a string query.
        $elasticaQueryString 	= new \Elastica\Query\QueryString();

        //'And' or 'Or' default : 'Or'
        $elasticaQueryString->setDefaultOperator('AND');
        $elasticaQueryString->setQuery('vadex');
        
        $elasticaQuery 		= new \Elastica\Query();
        $elasticaQuery->setQuery($elasticaQueryString);
        
        $elasticaFacet 	= new \Elastica\Facet\Terms('myFacetName');
        $elasticaFacet->setField('tags');
        $elasticaFacet->setSize(10);
        $elasticaFacet->setOrder('reverse_count');

        // Add that facet to the search query object.
        $elasticaQuery->addFacet($elasticaFacet);

        
        //Search on the index.
        $elasticaResultSet 	= $this->getIndex()->search($elasticaQuery);
        $hits 	= $elasticaResultSet->getResults();
        $facets = $elasticaResultSet->getFacets();
        $totalResults 		= $elasticaResultSet->getTotalHits();
        
        //var_dump($hits);
        var_dump($facets);die;

//
//
//        //add all, so that if no query it return everything
//        $allQuery = new Query\Range(new Index\Term('0','docId'),null,true);
//        $query->addSubquery($allQuery,true);
//        
//        foreach ($queryData as $filter => $values){
//            //if it is the main search
//            if ($filter === 'search' && $values){                
//                $values = explode(' ',$values);
//                $query->addSubquery($this->getQueryForTerms($values,NULL,true),true);
//            }
//            if ($filter === 'category' && $values){
//                $query->addSubquery($this->getQueryForTerms($values,$filter),true);
//            }
//            
//            //if it is a filter from the side
//            $filterConfig = $this->getFilterConfig()->get($filter);
//            if ($filterConfig !== NULL){
//                switch ($filterConfig['type']){
//                    case FiltersForm::$CHECKBOX :                       
//                        $query->addSubquery($this->getQueryForTerms($values,$filter),true);                        
//                        break;
//                    case FiltersForm::$RANGE :
//                        $query->addSubquery($this->getQueryForRange($values,$filter),true);     
//                        break;
//                    case FiltersForm::$SEARCH :
//                        $query->addSubquery($this->getQueryForTerms($values),true);
//                        break;
//                    default:
//                }
//            }
//        }
//        
//        //sort results and perform search
//        $hits = [];
//        try {
//            if ($sortOptions !== NULL){                
//                $hits = $index->find($query,$sortOptions['name'],$sortOptions['type'],$sortOptions['direction']);
//            } else {
//                $hits = $index->find($query);
//            }
//        }
//        catch (LuceneException $ex) {
//        }

        return $hits;
    }
    
    public function getCountNumberFromQuery($queryData)
    {
        return count($this->getResultsFromQuery($queryData));
    }
    
    
    public function getClient()
    {
        return $this->client;
    }
    
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }
    
    public function buildIndex()
    {
        $elasticaIndex = $this->getClient()->getIndex('learnlists');
        $elasticaIndex->create(
            array(
                'number_of_shards' => 4,
                'number_of_replicas' => 1,
                'analysis' => array(
                    'analyzer' => array(
                        'indexAnalyzer' => array(
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => array('lowercase', 'mySnowball')
                        ),
                        'searchAnalyzer' => array(
                            'type' => 'custom',
                            'tokenizer' => 'standard',
                            'filter' => array('standard', 'lowercase', 'mySnowball')
                        )
                    ),
                    'filter' => array(
                        'mySnowball' => array(
                            'type' => 'snowball',
                            'language' => 'German'
                        )
                    )
                )
            ),
            true
        );
        //Create a type
        $elasticaType = $elasticaIndex->getType('listquest');

        // Define mapping
        $mapping = new \Elastica\Type\Mapping();
        $mapping->setType($elasticaType);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        // Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'listId'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'questionNb'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'rating'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'author'    => array(
                'type' => 'object',
                'properties' => array(
                    'name'      => array('type' => 'string', 'include_in_all' => TRUE),
                    'role'  => array('type' => 'string', 'include_in_all' => TRUE),
                    'email'  => array('type' => 'string', 'include_in_all' => TRUE),
                ),
            ),
            'title'     => array('type' => 'string', 'include_in_all' => TRUE),
            'category'     => array('type' => 'string', 'include_in_all' => TRUE),
            'description'     => array('type' => 'string', 'include_in_all' => TRUE),
            'language'     => array('type' => 'string', 'include_in_all' => TRUE),
            'level'     => array('type' => 'string', 'include_in_all' => TRUE),
            'tags'     => array('type' => 'string', 'include_in_all' => TRUE),
            'questions'     => array('type' => 'string', 'include_in_all' => TRUE),
            'creationDate'  => array('type' => 'date', 'include_in_all' => FALSE),
            '_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
        ));
  
        $mapping->send();
        
        $this->setIndex($elasticaIndex);
        $this->hydrateIndex();
    }
    
    public function hydrateIndex()
    {
        $lists = $this->getListquestService()->fetchAll();
        $id =0;
        $elasticaType = $this->getIndex()->getType('listquest');
        $documents = [];
        foreach ($lists as $list) {
            $newDocument = $this->getNewListquestDocument();
            $documents[] = $newDocument->createDocumentFromListquest($id,$list);
            $id++;
        }
        $elasticaType->addDocuments($documents);
        $elasticaType->getIndex()->refresh();
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
    
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }
    
    public function getIndex()
    {
        if ($this->index === NULL)
        {
            $this->index = $this->getClient()->getIndex('learnlists');
                                             
        }
        return $this->index;
    }
    
    public function getNewListquestDocument()
    {
        return new ElasticaListquestDocument($this->getRatingService());        
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