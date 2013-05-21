<?php

namespace Application\Filter;

use Zend\Filter\Exception;
use Zend\Filter\AbstractFilter;
use Normalizer;

class Unaccent extends AbstractFilter
{
    public function filter($string)
    {
       if (extension_loaded('intl') === true){
            $string = Normalizer::normalize($string, Normalizer::FORM_KD);
        }

        if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false){
            $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|caron|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
        }

        return $string;
    }
}


