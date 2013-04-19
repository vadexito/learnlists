<?php

namespace LrnlSearch\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use LrnlSearch\Provider\ProvidesSearchService;

class SearchController extends AbstractActionController
{
    use ProvidesSearchService;
    
    public function indexAction()
    {
        $queryData = $this->getRequest()->getQuery();
        
        //init search bar
        $searchForm = $this->getServiceLocator()->get('learnlists-form-search');  
        $search = $this->params()->fromQuery('search',NULL);        
        $searchForm->setData(['search' => $search]);
        
        //init side filters
        $filterForm = $this->getServiceLocator()->get('learnlists-form-filter');
        $filterForm->initUrlInFilters($queryData);
        $filterForm->setData($queryData);
        
        //init results for main search
        $hits = $this->getSearchService()->getResultsFromQuery($queryData,[
            'name' => 'questionNb',
            'type' => SORT_NUMERIC,
            'direction' => SORT_DESC,
        ]);
        $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($hits));
        $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
        $paginator->setItemCountPerPage(10);
        
        return [
            'lists' => $paginator,
            'resultNb' => count($hits),
            'searchForm' => $searchForm,
            'filterForm' => $filterForm,
            'query' => (array)$queryData
        ];
        
    }
    
    public function buildAction()
    {
        $this->getSearchService()->buildIndex();        
        $this->redirect()->toRoute('home');
    }
    
    public function updateAction()
    {
        $this->getSearchService()->updateIndex();        
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

