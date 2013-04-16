<?php

namespace LrnlSearch\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZendSearch\Lucene\Document;
use ZendSearch\Lucene\Index;
use ZendSearch\Lucene;
use ZendSearch\Lucene\Exception as LuceneException;
use ZendSearch\Lucene\Search\Query;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive as UTF8NumCaseInsensitiveAnalyser;
use LrnlSearch\Document\ListquestDocument;


class SearchController extends AbstractActionController
{
    public function indexAction()
    {
        $search = $this->params()->fromQuery('search',NULL);
        $searchForm = $this->getServiceLocator()->get('learnlists-form-search');         
        $searchForm->setData(['search' => $search]);
        
        $searchService = $this->getServiceLocator()
                              ->get('learnlists-search-service-factory');
        $queryData = $this->getRequest()->getQuery();
        $hits = $searchService->getResultsFromQuery($queryData,[
            'name' => 'questionNb',
            'type' => SORT_NUMERIC,
            'direction' => SORT_DESC,
        ]);
        
        
        
        $filtersShow = [
            'filterTerm' => [
                _('level') => [_('advanced'),_('easy'),_('beginner')],
                _('language') => [_('german'),_('french'),_('polish')],
                _('authorName') => [_('madi'),_('vadex'),_('bgo')],
            ],
            'filterRange' => [
                [
                    'name' => _('questionNb'),
                    'label' => _('questions'),
                    'rangeMin' => 0,
                    'rangeMax' => 10,
                    'rangeStep' => 1,
                    'rangeValue' => '[5,10]',
                ]
            ]
        ];
        
        $filterForm = new \Zend\Form\Fieldset('filtersForm');
        foreach ($filtersShow['filterRange'] as $filter){
            $nameMin = $filter['name'].'Min';
            $nameMax = $filter['name'].'Max';
            
            $rangeValue = $filter['rangeValue'];
            $queryForUrl = clone $queryData;
            if ($queryData->get($nameMin) !== NULL && $queryData->get($nameMax)){
                $rangeValue = '['.$queryData->get($nameMin)
                    .','.$queryData->get($nameMax).']';
            } else {
                $queryForUrl->set($nameMin,$filter['rangeMin']);
                $queryForUrl->set($nameMax,$filter['rangeMax']);
            }
            
            
            
            
            $filterForm->add([
                'name' => $filter['name'],
                'type'  => 'text',                    
                'attributes' => [
                    'id'    => $filter['name'],
                    'type'  => 'text',    
                    'class' => 'span9',
                    'data-url' => $this->url()->fromRoute(
                            'lrnl-search',[],
                            ['query' => $queryForUrl->toArray()]
                    ),
                    'data-filterNameMin' => $nameMin,
                    'data-filterNameMax' => $nameMax,
                    'data-slider-min' => $filter['rangeMin'],
                    'data-slider-max' => $filter['rangeMax'],
                    'data-slider-value' => $rangeValue,
                    'data-slider-step' => $filter['rangeStep'],
                    'data-slider-selection' => 'after',
                    'data-slider-tooltip' => 'hide'
                ],
            ]);
        }
        foreach ($filtersShow['filterTerm'] as $filter => $values){
            $subForm = new \Zend\Form\Fieldset($filter);
            $filterForm->add($subForm);
                 
            foreach ($values as $value){ 
                
                //calculate the hit number of each option
                $filteredQuery = clone $queryData; 
                $filteredQuery->set($filter,[$value]);                
                $hitNb = count($searchService->getResultsFromQuery($filteredQuery));
                
                //url for checkbox (used by javascript)
                $filteredQueryforUrl = clone $queryData; 
                $filterValueInCurrentUrl = $queryData->get($filter);
                
                if ($filterValueInCurrentUrl === NULL){ //checkbox not checked, no other box checked
                    $filteredQueryforUrl->set($filter,[$value]);
                } else {
                    //convert string into array
                    if (!is_array($filterValueInCurrentUrl)){
                        $filteredQueryforUrl->set($filter,[$filterValueInCurrentUrl]);
                    }
                    //find if value is already in crossed checkbox
                    $keyValue = array_search($value,$filterValueInCurrentUrl);
                    if ($filterValueInCurrentUrl && is_int($keyValue)){
                        unset($filterValueInCurrentUrl[$keyValue]); //remove value if already ckecked
                    } else {          // checkbox not checked and other crossed values, we add the filter
                        $filterValueInCurrentUrl[] = $value;                        
                    }
                    $filteredQueryforUrl->set($filter,$filterValueInCurrentUrl);
                }
                
                $subForm->add([
                    'name' => $value,
                    'type'  => 'checkbox',                    
                    'attributes' => [
                        'id'    => $value,
                        'data-hitNb'    => $hitNb,
                        'data-url' => $this->url()->fromRoute(
                                'lrnl-search',[],
                                ['query' => $filteredQueryforUrl->toArray()]
                        ),
                        'class' => 'checkbox-filter'
                    ],
                    'options' => [
                        'label' => $value,
                        'use_hidden_element' => true,
                        'checked_value' => 'good',
                        'unchecked_value' => 'bad'
                    ]
                ]);
            }   
        }
        
        foreach ($queryData as $filter => $values){            
            if (!is_array($values) && $filterForm->get((string)$filter)) {
                $fielsetFilter = $filterForm->get((string)$filter);
                $fielsetFilter->get((string)$values)->setValue('good');
            }
            if (is_array($values) && $filterForm->get((string)$filter)) {
                $fielsetFilter = $filterForm->get((string)$filter);
                foreach ($values as $value) {
                    if ($fielsetFilter->get((string)$value)){
                        $fielsetFilter->get((string)$value)->setValue('good');
                    }
                }
            }   
        }
        
        return [
            'lists' => $hits,
            'searchForm' => $searchForm,
            'filterForm' => $filterForm,
            'filtersShow' => $filtersShow
        ];
        
    }
    
    public function buildAction()
    {
        $index = Lucene\Lucene::create('data/indexes/learnlists');
        Analyzer::setDefault(new UTF8NumCaseInsensitiveAnalyser);
        $lists = $this  ->getServiceLocator()
                        ->get('learnlists-listquestfactory-service')
                        ->fetchAll();
        
        $id =0;
        foreach ($lists as $list) {
            $index->addDocument((new ListquestDocument())->setData($id,$list));
            $id++;
        }
        $index->commit();
        $index->optimize();
        $this->redirect()->toRoute('home');
    }
    
    public function updateAction()
    {
        $indexPath = 'data/indexes/learnlists';
 
        // some internal method to retrieve the document from the database
        $document = getDocument();

        // create our index
        $index = Zend_Search_Lucene::open($indexPath);

        // find the document based on the indexed document_id field
        $term = new Index\Term($document->id, 'document_id');
        foreach ($index->termDocs($term) as $id)
            $index->delete($id);

        // re-add the document
        $index->addDocument(new ListquestDocument($listquest));

        // write the index to disk
        $index->commit();
    }
}

