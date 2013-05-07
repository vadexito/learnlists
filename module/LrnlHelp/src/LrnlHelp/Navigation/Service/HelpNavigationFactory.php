<?php

namespace LrnlHelp\Navigation\Service;

use Zend\Navigation\Service\DefaultNavigationFactory;

class HelpNavigationFactory extends DefaultNavigationFactory
{
    protected function getName()
    {
        return 'help_center';
    }
}
