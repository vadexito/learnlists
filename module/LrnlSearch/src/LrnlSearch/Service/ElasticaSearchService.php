<?php

namespace LrnlSearch\Service;

use ZendSearch\Lucene\Index;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Search\Query;
use Zend\Stdlib\Parameters;


use LrnlSearch\Document\ElasticaListquestDocument;
use LrnlSearch\Form\FiltersForm;
use LrnlSearch\Exception\SearchException;
use LrnlListquests\Entity\Listquest;

use WtRating\Service\RatingService;
use Traversable;

use Elastica\Client;

class ElasticaSearchService implements SearchServiceInterface
{
    protected $_indexPath;
    protected $_ratingService;
    protected $_filterConfig;
    protected $client;
    protected $index = NULL;
    
    public function __construct($indexPath,Traversable $filterConfig = NULL)
    {
        
        $client = new Client([
            'host' => 'localhost',
            'port' => 9200
        ]);
        
        $this->setClient($client);
        $this->setListquestService($listquestService);
        $this->setFilterConfig($filterConfig);
    }
    
    public function getQueryFromArray(Parameters $queryData)
    {
        $queryBool = new \Elastica\Query\Bool();
        
        if ($queryData->count() === 0){
            $queryBool->addMust(new \Elastica\Query\MatchAll());
        } else {
            foreach ($queryData as $filter => $values){
                //if it is the main search
                if ($filter === 'search' && $values){
                    $query = new \Elastica\Query\QueryString();
                    $query->setDefaultOperator('OR');                
                    $query->setQuery((string)$values);                
                    $queryBool->addMust($query);
                }

                if ($filter === 'category' && $values){
                    $query = new \Elastica\Query\Text();
                    $query->setField("category",$values);
                    $queryBool->addMust($query);
                }


                $filterConfig = $this->getFilterConfig()->get($filter);
                if ($filterConfig !== NULL){
                    switch ($filterConfig['type']){
                        case FiltersForm::$CHECKBOX :
                            if ($values){
                                foreach ($values as $value){
                                    $query = $this->getQueryForTerm($value,$filter);
                                    $queryBool->addMust($query);
                                }   
                            }                
                            break;
                        case FiltersForm::$RANGE :
                            //$query->addSubquery($this->getQueryForRange($values,$filter),true);     
                            break;
                        case FiltersForm::$SEARCH :
                            //$query->addSubquery($this->getQueryForTerms($values),true);
                            break;
                        default:
                    }
                }
            }
        }
        
        $elasticaQuery = new \Elastica\Query();  
        $elasticaQuery->setSize(100);
        $elasticaQuery->setQuery($queryBool);
        
        return $elasticaQuery;
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
        $elasticaQuery = $this->getQueryFromArray($queryData);
        
        if (is_array($sortOptions)){
            switch ($sortOptions['direction']){
                case SORT_DESC :
                    $order = 'desc';
                    break;
                case SORT_ASC :
                    $order = 'asc';
                    break;
                default:
                    $order = 'desc';
            }
            
            $elasticaQuery->addSort([
                $sortOptions['name'] => [
                    'order' => $order
                ]
            ]);
        }
        
        try {
            $elasticaResultSet = $this->getIndex()->search($elasticaQuery);
            $hits = $elasticaResultSet->getResults();
        }
        catch (\Exception $ex) {
            $hits = [];
        }
        
        return $hits;
    }
    
    public function getCountNumberFromQuery(Parameters $queryData)
    {
        return count($this->getResultsFromQuery($queryData));
    }
    
    
    protected function getQueryForTerm($value,$filter)
    {
        $query = new \Elastica\Query\Text();
	$query->setField($filter, $value);
        
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
    
    public function getClient()
    {
        return $this->client;
    }
    
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }
    
    public function buildIndex($lists)
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
            'authorName'     => array('type' => 'string', 'include_in_all' => TRUE),
            'authorRole'     => array('type' => 'string', 'include_in_all' => TRUE),
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
        $this->hydrateIndex($lists);
    }
    
    public function getFacet($facet,Parameters $queryData,Array $defaultValues)
    {
        $data = clone $queryData;
        if ($data->offsetExists($facet)){
            $data->offsetUnset($facet);
        }
        $query = $this->getSearchQueryFromUrlQuery($data);
        
        $elasticaFacet 	= new \Elastica\Facet\Terms('facet');
        $elasticaFacet->setField($facet);
        $elasticaFacet->setSize(3);
        $query->addFacet($elasticaFacet);
        
        $elasticaResultSet = $this->getIndex()->search($query);
        $elasticaFacets = $elasticaResultSet->getFacets();

        $facetValues = [];
        foreach ($elasticaFacets['facet']['terms'] as $elasticaFacet) {
            $facetValues[] = $elasticaFacet;
        }
        
        return $facetValues;
    }
    
    
    public function hydrateIndex($lists)
    {
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
}