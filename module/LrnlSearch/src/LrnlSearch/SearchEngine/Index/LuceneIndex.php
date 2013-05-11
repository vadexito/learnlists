<?php

namespace LrnlSearch\SearchEngine\Index;

use ZendSearch\Lucene;
use ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive as UTF8NumCaseInsensitiveAnalyser;
use ZendSearch\Lucene\Analysis\Analyzer\Analyzer;
use LrnlSearch\SearchEngine\Document\DocumentInterface;

class LuceneIndex implements IndexInterface
{
    protected $index;
    
    public function __construct()
    {
        $this->index = Lucene\Lucene::create($this->getIndexPath());
        Analyzer::setDefault(new UTF8NumCaseInsensitiveAnalyser);
    }
    
    public function addDocument(DocumentInterface $document){
        $this->getIndex()->addDocument($document);
    }
    
    public function getIndex()
    {
        return $this->index;
    }
    
    public function setIndex($index)
    {
        $this->index = $index;
    }
}