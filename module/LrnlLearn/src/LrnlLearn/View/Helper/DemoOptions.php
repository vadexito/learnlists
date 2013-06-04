<?php 

namespace LrnlLearn\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Json\Json;

class DemoOptions extends AbstractHelper
{
    public function __invoke($premium = false)
    {
        if ($premium){
            
        }
        
        $model = [
            'text' => $this->getView()->translate('Text of the question'),
            'title_list' => $this->getView()->translate('Title of the quiz'),
            'comment' => $this->getView()->translate('Comment area for the teacher'),
            'round_nb' => '4',
            'round_total' => '5',
            'nb_question' => '5',
            'nb_questions' => '20',
            'score' => $this->getView()->translate('24 points'),
            'checkMessageTitle' => $this->getView()->translate('Right'),
            'newPoints' => $this->getView()->translate('4 points'),
            'maxPoint' => '25',
            'comments' => $this->getView()->translate('Excellent. That was a quick and right answer'),
        ];
        
        
        
        return Json::encode([
            'model' => $model
        ]);
    }
}