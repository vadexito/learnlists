<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FooterController extends AbstractActionController
{
    public function indexAction()
    {
        $page = $this->params()->fromRoute('page', '');
        if (!$page) {
            return $this->redirect()->toRoute('home');
        }        
        $path = 'application/footer/'. $page . '.phtml';
        return (new ViewModel())->setTemplate($path);
    }
}
