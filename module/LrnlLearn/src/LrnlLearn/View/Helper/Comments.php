<?php 

namespace LrnlLearn\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Json\Json;

class Comments extends AbstractHelper
{
    public function __invoke($premium = false)
    {
        $comments = [
            '1' => [
                '1' => $this->getView()->translate('Always perfect'),
                '2' => $this->getView()->translate('You did perfect in less than 10 seconds now'),
                '3' => $this->getView()->translate('Perfect, no more mistake like last time!'),
                '4' => $this->getView()->translate('Perfect, no more mistakes like last time!'),
                '5' => $this->getView()->translate('Perfect and you did not ask the answer like last time!'),
            ],
            '2' => [
                '1' => $this->getView()->translate('Last time you were quicker'),
                '2' => $this->getView()->translate('Like last time, you are still too slow'),
                '3' => $this->getView()->translate('Great, no more mistake like last time but too slow!'),
                '4' => $this->getView()->translate('Great, no more mistakes like last time but too slow!'),
                '5' => $this->getView()->translate('Great event if too slow but you did not ask the answer like last time!'),
            ],
            '3' => [
                '1' => $this->getView()->translate('Last time you were quicker'),
                '2' => $this->getView()->translate('Like last time, you are still too slow'),
                '3' => $this->getView()->translate('Great, no more mistake like last time but too slow!'),
                '4' => $this->getView()->translate('Great, no more mistakes like last time but too slow!'),
                '5' => $this->getView()->translate('Great event if too slow but you did not ask the answer like last time!'),
            ],
            '4' => [
                '1' => $this->getView()->translate('Last time you were quicker'),
                '2' => $this->getView()->translate('Like last time, you are still too slow'),
                '3' => $this->getView()->translate('Great, no more mistake like last time but too slow!'),
                '4' => $this->getView()->translate('Great, no more mistakes like last time but too slow!'),
                '5' => $this->getView()->translate('Great event if too slow but you did not ask the answer like last time!'),
            ],
            '5' => [
                '1' => $this->getView()->translate('Last time you were quicker'),
                '2' => $this->getView()->translate('Like last time, you are still too slow'),
                '3' => $this->getView()->translate('Great, no more mistake like last time but too slow!'),
                '4' => $this->getView()->translate('Great, no more mistakes like last time but too slow!'),
                '5' => $this->getView()->translate('Great event if too slow but you did not ask the answer like last time!'),
            ]
        ];
        
        return Json::encode($comments);
    }
}