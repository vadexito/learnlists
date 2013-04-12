<?php 

namespace VxoOffers\View\Helper;

use Zend\View\Helper\AbstractHelper;

class PriceCell extends AbstractHelper
{
    public function __invoke()
    {
        $priceCell = '';
        
        $offerName = 'Basic';
        $offerDescription = 'Great for students to get started';
        $pricing = 'FREE';
        $teacherComments = 'no limit';
        $accessQuizz = [
            'student' => 'no limit',
            'teacher' => 'no limit',
        ];
        $multiRoundQuizz = 'no limit';
        
        
        
        $top = '<div class="top">'
            .'<h2 class="offers">'.$offerName.'</h2>'
            .'<h4 class="offers">'.$offerDescription.'</h4>
            </div>';
        $priceCell .=$top;
        
        $price = '<div class="price">'
            .'<h3 class="offers">'.$pricing.'</h3>
            </div>';
        $priceCell .=$price;
        
        $volume = '<div class="volumes">
                <div class="volume requests">
                    <div class="title">Requests</div>
                    <div class="number">1 million/month</div>
                </div>
                <div class="volume pushes">
                    <div class="title">Pushes</div>
                    <div class="number">1 million/month</div>
                </div>
                <div class="volume files">
                    <div class="title">Burst Limit</div>
                    <div class="number">20/second</div>
                </div>
            </div>';
        $priceCell .=$volume;
        
        $coreFeatures = '<div class="core_features">
                <div class="icon"></div>
                <div class="title">Parse Core Platform</div>
            </div>';
        $priceCell .=$coreFeatures;
        
        $chooseButton = '<div class="choose_plan_button">Choose plan</div>';
        $priceCell .=$chooseButton;
        
        return $priceCell;
    }
}