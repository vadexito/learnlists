<?php

namespace Question\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ReplaceBlank extends AbstractHelper
{
    public function __invoke($text)
    {
        $output = sprintf($text, 'BLANK');
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }
}