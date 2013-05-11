<?php

namespace LrnlSearch\SearchEngine\Index;

use LrnlSearch\SearchEngine\Document\DocumentInterface;

interface IndexInterface
{
    
    public function addDocument(DocumentInterface $document);
}