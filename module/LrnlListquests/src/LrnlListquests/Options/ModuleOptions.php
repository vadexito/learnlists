<?php

namespace LrnlListquests\Options;

use Zend\Stdlib\AbstractOptions;

class ModuleOptions extends AbstractOptions 
{
    /**
     * @var string
     */
    protected $questionresultEntityClass = 'LrnlListquests\Entity\Questionresult';

    /**
     * @var array
     */
    protected $scoreTable = [
        '0' => '0',
        '1' => '4',
        '2' => '3',
        '3' => '2',
        '4' => '1',
        '5' => '0',
    ];
    
    protected $categories = [];
    
    protected $redirectRouteAfterListquestCrud;
    
    public function getRedirectRouteAfterListquestCrud()
    {
        return $this->redirectRouteAfterListquestCrud;        
    }
    
    public function setRedirectRouteAfterListquestCrud($redirectRouteAfterListquestCrud)
    {
        $this->redirectRouteAfterListquestCrud = $redirectRouteAfterListquestCrud;  
        return $this;
    }
    
    
    public function getScoreTable()
    {
        return $this->scoreTable;        
    }
    
    public function setScoreTable(Array $scoreTable)
    {
        $this->scoreTable = $scoreTable;  
        return $this;
    }
    
    public function getCategories()
    {
        return $this->categories;        
    }
    
    public function setCategories(Array $categories)
    {
        $this->categories = $categories;  
        return $this;
    }
    
    public function setQuestionresultEntityClass($questionresultEntityClass)
    {
        $this->questionresultEntityClass = $questionresultEntityClass;
        return $this;
    }

    public function getQuestionresultEntityClass() 
    {
        return $this->questionresultEntityClass;
    }
}
