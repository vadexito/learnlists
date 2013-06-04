<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;


class ThumbLine extends AbstractHelper
{
    protected $nbMaxLine_default = 2;    
    protected $nbElementPerline_default = 2;
    protected $openingTags_default = '<div>';
    protected $closingTags_default = '</div>';
    
    public function __invoke($elements,array $options)
    {
        $nbMaxLine = isset($options['nbMaxLine_default']) ? 
            $options['nbMaxLine_default'] : $this->nbMaxLine_default;
        $nbElementPerLine = isset($options['nbElementPerline']) ? 
            $options['nbElementPerline'] : $this->nbElementPerline_default;
        $openingTags = isset($options['openingTags']) ? 
            $options['openingTags'] : $this->openingTags_default;
        $closingTags = isset($options['closingTags']) ? 
            $options['closingTags'] : $this->closingTags_default;
        
        $render = '';
        $thumbLines = 0;
        $openCycle = [$openingTags];
        $closeCycle = [$closingTags];
        $cycleLines = [1]; 

        for ($i=0 ; $i < $nbElementPerLine-1 ; $i++){
            $cycleLines[] = 0;
            $closeCycle[] = '';
            $openCycle[] = '';
        }
        $cycleLines = array_reverse($cycleLines);
        $closeCycle = array_reverse($closeCycle);

        foreach ($elements as $element) {
            
            $render .= (string)$this->getView()->cycle($openCycle,'openTagQuizes')->next();
            $render .= $element;
            $end = (string)$this->getView()->cycle($closeCycle,'closingTagQuizes')->next();
            $render .= $end;   
            
            $thumbLines += (string)$this->getView()->cycle($cycleLines,'nbLineQuizes')->next();
            if ($thumbLines === $nbMaxLine){
                break;
            }
        }

        if ($end === ''){
            $render .= $closingTags; 
            
        }

        return $render;
    }
}