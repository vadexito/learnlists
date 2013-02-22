<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;

class LocaleController extends AbstractActionController
{
    public function changeAction()
    {
        $locale = $this->params()->fromRoute('locale', '');
        if (!$locale) {
            return $this->redirect()->toRoute('home');
        }
        $session = new Container('learnlists_locale');
        $session->locale = $locale;
        
        return $this->redirect()->toRoute('home');
    }
}
