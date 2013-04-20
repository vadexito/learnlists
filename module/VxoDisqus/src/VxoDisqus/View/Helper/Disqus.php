<?php 

namespace VxoDisqus\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\Stdlib\Parameters;

class Disqus extends AbstractHelper
{
    protected $_config ;

    public function __invoke()
    {
        $config = new Parameters($this->getConfig());
        $script = '';
        if ($config->get('developer')){
            $script .= "var disqus_developer = 1 \n";  
        }            
        $script .= sprintf("var disqus_shortname = '%s';\n",
                 $config->get('shortname')); 

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
    
    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }
    
    public function getConfig()
    {
        return $this->_config;
    }
}