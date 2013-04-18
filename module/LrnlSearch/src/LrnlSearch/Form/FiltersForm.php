<?php

namespace LrnlSearch\Form;

use Zend\Form\Form;
use LrnlSearch\Form\FilterTermCheckboxesFieldset;
use LrnlSearch\Form\FilterRangeSliderElement;
use LrnlSearch\Form\FilterTermCheckboxElement;
use LrnlSearch\Service\SearchService;

use Zend\Form\Element\Text;
use Zend\Form\Fieldset;

class FiltersForm extends Form
{
    public function __construct($name = NULL,SearchService $searchService)
    {
        parent::__construct($name);
        
        $level = new FilterTermCheckboxesFieldset('level');
        $level->setLabel(_('level'))
              ->add(new FilterTermCheckboxElement(_('advanced'),$searchService))
              ->add(new FilterTermCheckboxElement(_('easy'),$searchService))
              ->add(new FilterTermCheckboxElement(_('beginner'),$searchService));
        
        $language = new FilterTermCheckboxesFieldset('language');
        $language->setLabel(_('language'))
              ->add(new FilterTermCheckboxElement(_('german'),$searchService))
              ->add(new FilterTermCheckboxElement(_('french'),$searchService))
              ->add(new FilterTermCheckboxElement(_('polish'),$searchService));
        
        $authorName = new FilterTermCheckboxesFieldset('authorRole');
        $authorName->setLabel(_('author'))
              ->add(new FilterTermCheckboxElement(_('teacher'),$searchService))
              ->add(new FilterTermCheckboxElement(_('student'),$searchService));
        
        $questionNb = new Fieldset('questionNb');
        $questionNb->setAttribute('data-filterType','range');
        $questionNbElement = new FilterRangeSliderFormElement('questionNb');
        $questionNbElement  ->setLabel(_('questions'))
                            ->setAttributes([
                             'data-slider-min' => 0,
                             'data-slider-max' => 50,
                             'data-slider-value' => '[0,50]',
                             'data-slider-step' => 1,
        ]);
        $questionNb->add($questionNbElement);
        
        $rating = new Fieldset('rating');
        $rating->setAttribute('data-filterType','range');
        $ratingElement = new FilterRangeSliderFormElement('rating');
        $ratingElement  ->setLabel(_('rating'))
                            ->setAttributes([
                             'data-slider-min' => 0,
                             'data-slider-max' => 50,
                             'data-slider-value' => '[0,50]',
                             'data-slider-step' => 1,
        ]);
        $rating->add($ratingElement);
        
        $keyword = new Fieldset('keyword');
        $keyword->setAttribute('data-filterType','simpleSearch');
        $keywordElement = new Text('keyword');
        $keywordElement ->setLabel(_('Keywords'))
                        ->setAttributes([
                            'type' => 'text',
                            'class' => 'span9',
                            'id' => 'keywords-bar',
                            'placeholder' => _('Enter a keyword'),
        ]);
        $keyword->add($keywordElement);
        
        $this->add($level)
             ->add($language)
             ->add($authorName)
             ->add($rating)
             ->add($questionNb)
             ->add($keyword);
    }
    
    public function initUrlInFilters($queryData)
    {
        foreach ($this->getFieldsets() as $fieldset){            
            foreach ($fieldset->getElements() as $element){
                if (method_exists($element, 'setQueryUrl')){
                    $element->setQueryUrl(
                            $queryData,
                            $fieldset->getName()
                    );
                }
             }
        }
    }
    
    
}