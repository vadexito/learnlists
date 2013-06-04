<?php

namespace VxoUtils\Filter;

use Zend\Filter\AbstractFilter;

class TruncateString extends AbstractFilter
{
    protected $append = '...';
    protected $limit = 15;
    
    public function filter($value)
    {
       return $this->limit_text($value,$this->limit);       
    }
    
    protected function setAppend($append)
    {
        $this->append = $append;
    }
    
    protected function setLimit($limit)
    {
        $this->limit = $limit;
    }
    
    public function limit_text($text, $len) 
    {
        if (strlen($text) < $len) {
            return $text;
        }
        $text_words = explode(' ', $text);
        $out = null;


        foreach ($text_words as $word) {
            if ((strlen($word) > $len) && $out == null) {

                return substr($word, 0, $len) . $this->append;
            }
            if ((strlen($out) + strlen($word)) > $len) {
                return $out . $this->append;
            }
            $out.=" " . $word;
        }
        return $out;
    }
}


