<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;
use LrnlListquests\Entity\ListquestInterface;

class ListquestThumbnail extends AbstractHelper
{
    public function __invoke(ListquestInterface $listquest,array $options = NULL)
    {
        $wrapTag = 'li';
        
        $picture = '<img src="'
            .$this->getView()->listquestPictureUrl($listquest) 
            .'" alt="'
            .ucfirst($this->getView()->escapeHtml($listquest->getTitle())).'">';
       $link = '<a href="#" class="thumbnail">'
            .$picture
            .'</a>';
        $title = '<h4 class="text-center span6 title_image">'
            . ucfirst($this->getView()->escapeHtml($listquest->getTitle()))
            .'</h4>';
   
        return '<'.$wrapTag.' class="span3">'.$link.$title.'</'.$wrapTag.'>';
    }
}