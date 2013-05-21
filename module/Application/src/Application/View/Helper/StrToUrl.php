<?php 

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Application\Filter\Unaccent;

class StrToUrl extends AbstractHelper
{
    public function __invoke($string)
    {
        return $this->slug($string);
    }
    
    public function slug($string, $slug = '-', $extra = null)
    {
        $unaccent = new Unaccent();
        return strtolower(trim(preg_replace('~[^0-9a-z' . preg_quote($extra, '~') . ']+~i', $slug, $unaccent->filter($string)), $slug));
    }
}