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
    
    protected $tmpPictureUploadDir = './data/tmpuploads/';
    
    protected $redirectRouteAfterListquestCrud = 'lrnl-search';
    
    protected $listquestEntityClass = 'LrnlListquests\Entity\Listquest';
    
    public function getListquestEntityClass()
    {
        return $this->listquestEntityClass;        
    }
    
    public function setListquestEntityClass($listquestEntityClass)
    {
        $this->listquestEntityClass = $listquestEntityClass;  
        return $this;
    }
    
    public function getTmpPictureUploadDir()
    {
        return $this->tmpPictureUploadDir;        
    }
    
    public function setTmpPictureUploadDir($tmpPictureUploadDir)
    {
        $this->tmpPictureUploadDir = $tmpPictureUploadDir;  
        return $this;
    }
    
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
