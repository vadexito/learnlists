<?php 

namespace VxoDisqus\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\Parameters;
use VxoDisqus\Options\ModuleOptions;
use VxoDisqus\Exception\InvalidArgumentException;

class Disqus extends AbstractHelper
{
    protected $options ;

    /**
     * See http://help.disqus.com/customer/portal/articles/472098-javascript-configuration-variables for javascript parameters
     * 
     * @param type $identifier
     * @param type $title
     * @param type $url
     * @return string
     * @throws InvalidArgumentException
     */
    public function __invoke($identifier = '',$title = '',$url = '')
    {
        if (!$this->getOptions()->getEnabled()){
           return '';
        }
        $script = '';
        
        $shortName = $this->getOptions()->getShortName();
        if (!$shortName || !is_string($shortName)){
            throw new InvalidArgumentException('Please provide a short name (string)in the configuration file.');
        }        
        $script .= sprintf("var disqus_shortname = '%s';\n",
                 $shortName); 
        
        if ($identifier){
            $script .= sprintf("var disqus_identifier = '%s';\n",
                 $identifier); 
        }
        if ($title){
            $script .= sprintf("var disqus_title = '%s';\n",
                 $title); 
        }
        if ($url){
            $script .= sprintf("var disqus_url = '%s';\n",
                 $url); 
        }

        $script .= <<<SCRIPT
(function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();\n
SCRIPT;
        $this->getView()->inlineScript()->appendScript($script);
        
        $render = '<div id="disqus_thread"></div>'."\n"
        .'<noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>'
        .'<a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>';
        
        return $render;
    }
    
    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;
        return $this;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
}