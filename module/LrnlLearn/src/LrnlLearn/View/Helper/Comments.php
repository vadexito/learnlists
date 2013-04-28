<?php 

namespace LrnlLearn\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Json\Json;

class Comments extends AbstractHelper
{
    public function __invoke($premium = false)
    {
        //comments for each case of answer,and former anwer
        $index = ['1','2','3','4','5'];
        
        $currentResult = [
            '1' => $this->getView()->translate('Max point!'),
            '2' => $this->getView()->translate('Almost max point!'),
            '3' => $this->getView()->translate('One mistake!'),
            '4' => $this->getView()->translate('Several mistakes!'),
            '5' => $this->getView()->translate('Now try to learn!'),
        ];
        
        $comparison = [
            '-1' => $this->getView()->translate('You are imroving!'),
            '0' => $this->getView()->translate('Like last time!'),
            '1' => $this->getView()->translate('Last time was better!'),
        ];
        
        foreach ($currentResult as $i => $result){
            foreach ($index as $j){
                if ($j > $i){
                    $comments[$i][$j] = $result.$comparison[-1];
                }
                if ($j === $i){
                    $comments[$i][$j] = $result.$comparison[0];
                }
                if ($j < $i){
                   $comments[$i][$j] = $result.' '.$comparison[1]; 
                }
            }
        }
        
        if ($premium) {
            $comments = [
                '1' => [
                    '1' => $this->getView()->translate('Perfect, like last time!'),
                    '2' => $this->getView()->translate('You did perfect in less than 10 seconds now!'),
                    '3' => $this->getView()->translate('This time, no more mistake like last time!'),
                    '4' => $this->getView()->translate('Perfect, no more mistakes like last time!'),
                    '5' => $this->getView()->translate('Perfect,this time you did not ask the answer!'),
                ],
                '2' => [
                    '1' => $this->getView()->translate('Last time you were quicker...'),
                    '2' => $this->getView()->translate('Like last time, you are still too slow'),
                    '3' => $this->getView()->translate('Great, no more mistake like last time but too slow!'),
                    '4' => $this->getView()->translate('Great, no more mistakes like last time but too slow!'),
                    '5' => $this->getView()->translate('Great even if too slow but you did not ask the answer like last time!'),
                ],
                '3' => [
                    '1' => $this->getView()->translate('You forgot...last time was perfect, you did no mistake!'),
                    '2' => $this->getView()->translate('You forgot...last time you did no mistake!'),
                    '3' => $this->getView()->translate('Still one mistake!'),
                    '4' => $this->getView()->translate('Better last time you did more mistakes!'),
                    '5' => $this->getView()->translate('Better because you did not ask the answer like last time!'),
                ],
                '4' => [
                    '1' => $this->getView()->translate('You forgot...last time was perfect, you did no mistake'),
                    '2' => $this->getView()->translate('You forgot...last time you did no mistake!'),
                    '3' => $this->getView()->translate('You are doing more mistakes!'),
                    '4' => $this->getView()->translate('Still mistakes!'),
                    '5' => $this->getView()->translate('Better because you did not ask the answer like last time!'),
                ],
                '5' => [
                    '1' => $this->getView()->translate('You forgot this one you knew perfectly, focus on it!'),
                    '2' => $this->getView()->translate('You forgot this one you knew well, focus on it!'),
                    '3' => $this->getView()->translate('You forgot this one you knew, focus on it!'),
                    '4' => $this->getView()->translate('You forgot this one you knew quite well, focus on it!'),
                    '5' => $this->getView()->translate('Still you do not know it at all, focus on it!'),
                ]
            ];
        }
        
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