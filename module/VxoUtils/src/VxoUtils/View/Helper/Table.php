<?php 

namespace VxoUtils\View\Helper;

use Zend\View\Helper\AbstractHtmlElement;


/**
 * 
 * 
 * example : 
 * $dataTable = [
            'thead' => [
                'attribs' => $theadAttribs,
                'th' => [
                    $this->getView()->translate('text'),
                    $this->getView()->translate('answer'),
                    $this->getView()->translate('comment'),
                 ],
            ],
            'tbody' => [
                'tr' => [
                    'attribs' => $trAtribs,
                    'th' => [ 
                        $this->getView()->translate('text'),
                        $this->getView()->translate('answer'),
                        [
                        'attribs' => ['class' => 'btn-group'],
                        'content' => $this->getCrudButtons($question->get('id')->getValue()),
                        ],
                    ],
                ],
                'tr' => [
                    'attribs' => $trAtribs,
                    'th' => [ 
                        $this->getView()->translate('text'),
                        $this->getView()->translate('answer'),
                        [
                        'attribs' => ['class' => 'btn-group'],
                        'content' => $this->getCrudButtons($question->get('id')->getValue()),
                        ],
                    ],
                ],
            ],
        ];
 * 
 * 
 */
class Table extends AbstractHtmlElement
{
    public function __invoke(array $dataTable)
    {
        $thead = '';
        $tbody = '';

        foreach ($dataTable['thead']['th'] as $th){
            $thead .= '<th>'.$th.'</th>';
        }
        if (isset($dataTable['thead']['attribs'])){
            $thead = '<thead '
                 .$this->htmlAttribs($dataTable['thead']['attribs']).'>'
                 .$thead.'</thead>';
        } 

        foreach ($dataTable['tbody']['tr'] as $tr){
            $tds = '';
            foreach ($tr['td'] as $td){
                if (is_array($td)){
                    if (isset($td['attribs'])){
                        $tds .= '<td'
                        .$this->htmlAttribs($td['attribs'])
                        .'>'.$td['content'].'</td>'; 
                    }
                } else {
                    $tds .= '<td>'.$td.'</td>';
                }
            }
            $trAttribs = [];
            if (isset($tr['attribs'])){
                $trAttribs = $tr['attribs'];
            }
            $tbody .= '<tr '
                .$this->htmlAttribs($trAttribs).'>'
                .$tds.'</tr>';
        }        
        $tbody = '<tbody>'.$tbody.'</tbody>';
        
        
        return '<table>'.$thead.$tbody.'</table>';
    }
}