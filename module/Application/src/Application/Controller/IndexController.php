<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Tag\ItemList;
use Zend\Tag\Item;
use Zend\Tag\Cloud;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $cloud = new Cloud(array(
            'tagDecorator' => array(
                'decorator' => 'htmltag',
                'options' => array(
                    'minFontSize' => '20',
                    'maxFontSize' => '50',
                    'htmlTags' => array(
                        'li' => array('class' => 'my_custom_class')
                    )
                )
            ),
            'tags' => array(
               array('title' => 'Code', 'weight' => 50,
                     'params' => array('url' => '/tag/code')),
               array('title' => 'Zend Framework', 'weight' => 1,
                     'params' => array('url' => '/tag/zend-framework')),
               array('title' => 'PHP', 'weight' => 5,
                     'params' => array('url' => '/tag/php')),
           )
        ));

        // Render the cloud
        echo $cloud;
        return new ViewModel();
    }
}
