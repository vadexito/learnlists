<?php

namespace LrnlSearch\Service;

use Zend\Stdlib\Parameters;

use LrnlSearch\Exception\SearchException;
use LrnlListquests\Entity\Listquest;

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
    public function getResultsFromQuery(Parameters $queryData,$sortOptions = NULL);    
    public function getCountNumberFromQuery(Parameters $queryData); 
    public function getFacet($facet,Parameters $queryData,Array $defaultValues);
    public function buildIndex($lists);       
    public function updateIndex(Listquest $listquest);    
    public function deleteFromIndex($listquestId);
}