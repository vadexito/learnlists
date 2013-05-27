<?php 

namespace LrnlLearn\View\Helper;

use Zend\Form\FormInterface;
use Zend\View\Helper\AbstractHelper;

class ReviewForm extends AbstractHelper
{
    public function __invoke(FormInterface $form)
    {
        return $this;
    }
    
    public function __toString()
    {
        return 'u';
    }
}