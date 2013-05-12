<?php 

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Normalizer;


class StrToUrl extends AbstractHelper
{
    public function __invoke($string)
    {
        return $this->slug($string);
    }
    
    public function unaccent($string)
    {
        if (extension_loaded('intl') === true){
            $string = Normalizer::normalize($string, Normalizer::FORM_KD);
        }

        if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false){
            $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|caron|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
        }

        return $string;
    }
    
    public function slug($string, $slug = '-', $extra = null)
    {
        return strtolower(trim(preg_replace('~[^0-9a-z' . preg_quote($extra, '~') . ']+~i', $slug, $this->unaccent($string)), $slug));
    }
}