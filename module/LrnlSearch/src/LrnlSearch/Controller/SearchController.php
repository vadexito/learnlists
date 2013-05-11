<?php

namespace LrnlSearch\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use LrnlSearch\Provider\ProvidesSearchService;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

class SearchController extends AbstractActionController
{
    use ProvidesSearchService;
    
    public function indexAction()
    {
        $queryData = $this->getRequest()->getQuery();
        
        //init search bar
        $searchForm = $this->getServiceLocator()->get('learnlists-form-search');  
        $search = $this->params()->fromQuery('search',NULL);        
        $category = $this->params()->fromQuery('category',NULL);
        $searchForm->setData(['search' => $search,'category' => $category]);
        
        //init results for main search
        $hits = $this->getSearchService()->getResultsFromQuery($queryData,[
            'name' => 'questionNb',
            'type' => SORT_NUMERIC,
            'direction' => SORT_DESC,
        ]);
        
        //init side filters
        $filterForm = $this->getServiceLocator()->get('learnlists-form-filter');
        $filterForm->initFilters($queryData);        
        $filterForm->setData($queryData->toArray());
        
        $paginator = new Paginator(new ArrayAdapter($hits));
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);
        
        return [
            'lists' => $paginator,
            'resultNb' => count($hits),
            'searchForm' => $searchForm,
            'filterForm' => $filterForm,
            'query' => $queryData ? $queryData->toArray() : []
        ];
        
    }
    
    public function buildAction()
    {
        $lists = $this  ->getServiceLocator()
                        ->get('learnlists-listquestfactory-service')
                        ->fetchAll();
        $this->getSearchService()->buildIndex($lists); 
        
        $this->redirect()->toRoute('home');
    }
    
    public function getSearchService()
    {
        if ($this->_searchService === NULL) {
            $this->setSearchService(
                    $this->getServiceLocator()
                         ->get('learnlists-search-service-factory')
            );
        }
        return $this->_searchService;
    }
}

