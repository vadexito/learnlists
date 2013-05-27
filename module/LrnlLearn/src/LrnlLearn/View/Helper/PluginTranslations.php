<?php 

namespace LrnlLearn\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Json\Json;

class PluginTranslations extends AbstractHelper
{
    public function __invoke()
    {
        $raty = [
            'hints' => [
                $this->getView()->translate('bad'),
                $this->getView()->translate('bad'),
                $this->getView()->translate('poor'),
                $this->getView()->translate('poor'),
                $this->getView()->translate('regular'),
                $this->getView()->translate('regular'),
                $this->getView()->translate('good'),
                $this->getView()->translate('good'),
                $this->getView()->translate('very good'),
                $this->getView()->translate('gorgeous')
            ]
        ];
        
        $introJs = [
            'skipLabel' => $this->getView()->translate('skip'),
            'nextLabel' => $this->getView()->translate('next'),
            'prevLabel' => $this->getView()->translate('back'),
            'doneLabel' => $this->getView()->translate('done'),
        ];
        
        return Json::encode([
            'raty' => $raty,
            'introJs' => $introJs,
        ]);
    }
}