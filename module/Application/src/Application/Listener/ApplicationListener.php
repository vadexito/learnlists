<?php

namespace Application\Listener;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\JsonModel;
use Zend\View\Model\FeedModel;
use Zend\View\Model\ConsoleModel;

class ApplicationListener implements ListenerAggregateInterface
{
    protected $listeners = [];

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_RENDER, 
            array($this, 'renderLayoutSegments'),
            -100
        );
        $this->listeners[] = $events->attach(
            MvcEvent::EVENT_DISPATCH, 
            array($this, 'setupCurrency'),
            -100
        );
    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function renderLayoutSegments(EventInterface $e)
    {
        $viewModel = $e->getViewModel();   
        var_dump($viewModel);die;
        
        if (($viewModel instanceof JsonModel)
                || ($viewModel instanceof FeedModel) 
                || ($viewModel instanceof ConsoleModel) ){
            return $e->getResponse();
        }
        
        $header = new ViewModel();
        $header->setTemplate('layout/header');
        $viewModel->addChild($header, 'header');
        
        $footer = new ViewModel();
        $footer->setTemplate('layout/footer');
        $viewModel->addChild($footer, 'footer');
        
        return $e->getResponse();
    }

    public function setupCurrency(EventInterface $e)
    {
        // get service manager
        $serviceManager = $e->getApplication()->getServiceManager();
        $viewManager    = $serviceManager->get('viewmanager');
        
        // setup currency view helper
        $helper = $viewManager->getRenderer()->plugin('currencyformat');
        $helper->setCurrencyCode('EUR');
        $helper->setShouldShowDecimals(true);
    }
}
