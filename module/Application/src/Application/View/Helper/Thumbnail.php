<?php 

namespace Application\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;
use Zend\View\Exception\InvalidArgumentException;

class Thumbnail extends AbstractHtmlElement
{
    protected $_attribs;
    
    protected $_title = NULL;
    
    protected $_sizes = [
        'large' => ['width' => '260px','height' => '180px'],
        'medium' => ['width' => '150px','height' => '110px'],
        'small' => ['width' => '114px','height' => '80px'],
    ];
    
    protected $_size = 'large';
    
    public function __invoke(array $attribs,$options = NULL)
    {
        if (!$attribs || !is_array($attribs) || !isset($attribs['src'])){
            throw new InvalidArgumentException('You should provide an array with at least an src for the thumbanil component');
        }
        
        if (isset($attribs['class'])){
            $attribs['class'] = $attribs['class']. ' '.'thumbnail';
        } else {
            $attribs['class'] = 'thumbnail';
        }
        
        if (isset($options['title'])){
            $this->_title = $options['title'];
        }
        
        if (isset($options['size']) 
            && in_array($options['size'],array_keys($this->_sizes))){
            $this->_size = $options['size'];
        }
        
        if (!isset($attribs['style'])){
            $attribs['style'] = 'width : '
                .$this->_sizes[$this->_size]['width']
                .'; height: '
                .$this->_sizes[$this->_size]['height'];
        }
        $this->_attribs = $attribs;
        return $this;
    }
    
    public function __toString()
    {
        $thumbnail = '<img '.$this->htmlAttribs($this->_attribs).'>';
        if ($this->_title){
            $thumbnail .= '<h4 class="text-center span6 title_image">'
                .$this->_title.'</h4>';
        }
        
        return $thumbnail;
    }
}