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

interface SearchServiceInterface
{
    
    /**
     * 
     * @param type $queryData containing all the parameter for a query to 
     * lucene index
     * @param type $sortOptions for sorting options
     * @return array of hits (lucene hit)
     * @throws SearchException
     */
    public function getResultsFromQuery($queryData,$sortOptions = NULL);    
    public function getCountNumberFromQuery($queryData);    
    public function buildIndex();       
    public function updateIndex(Listquest $listquest);    
    public function deleteFromIndex($listquestId);
}