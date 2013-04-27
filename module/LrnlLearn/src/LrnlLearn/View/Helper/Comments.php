<?php 

namespace LrnlLearn\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Json\Json;

class Comments extends AbstractHelper
{
    public function __invoke($premium = false)
    {
        //comments for each case of answer,and former anwer
        
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
        
        return Json::encode([
            'results' => [
                'right' => $this->getView()->translate('Right'),
                'wrong' => $this->getView()->translate('Wrong'),
            ],
            'comments' => $comments,
            'newPoints' => [
                '1' => '+ 4 '.$this->getView()->translatePlural('point','points',4),
                '2' => '+ 3 '.$this->getView()->translatePlural('point','points',3),
                '3' => '+ 2 '.$this->getView()->translatePlural('point','points',2),
                '4' => '+ 1 '.$this->getView()->translatePlural('point','points',1),
                '5' => '+ 0 '.$this->getView()->translatePlural('point','points',0),
            ],
        ]);
    }
}