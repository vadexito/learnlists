<?php

namespace LrnlSearch\Traits;

trait LuceneSearchTrait
{
    public function convertNumToString($number,$numberSize = 10)
    {
         return str_pad($number,$numberSize,0,STR_PAD_LEFT);
    }
}