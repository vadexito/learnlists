<?php 

namespace LrnlListquests\View\Helper;

use Zend\View\Helper\AbstractHelper;

class QuestionsFieldset extends AbstractHelper
{
    public function __invoke($questionsCollectionOrArray,$buttons=true)
    {
        $theadAttribs = [];
        if (count($questionsCollectionOrArray) === 0){
            $theadAttribs['style'] = 'display:none;';
        }
        
        $dataTable = [
            'thead' => [
                'attribs' => $theadAttribs,
                'th' => [
                    ucfirst($this->getView()->translate('text')),
                    ucfirst($this->getView()->translate('answer')),
                    ucfirst($this->getView()->translate('comment')),
                 ],
            ],
        ];
        
        foreach ($questionsCollectionOrArray as $question){
            $dataTable['tbody']['tr'][] = [
                'attribs' => [
                    'class' => 'populate'
                ],
                'td' => [
                    $this->getView()->formInput($question->get('text')),
                    $this->getView()->formInput($question->get('answer')),
                    $this->getView()->formInput($question->get('comment'))
                        .$this->getView()->formHidden($question->get('id')),
                    [
                        'attribs' => ['class' => 'btn-group'],
                        'content' => $buttons? $this->getCrudButtons($question->get('id')->getValue()) : '',
                    ],
                ],
            ];
        }
        
        return $this->getView()->table($dataTable);
    }
    
    public function getCrudButtons($questionId)
    {
        $urlEdit = $this->getView()->url(
            'listquests/question/edit',
            ['id' => $questionId]
        );
        $urlDelete = $this->getView()->url(
            'listquests/question/delete',
            ['id' => $questionId]
        );
        $titleEdit = $this->getView()->translate('Edit question');
        $titleDelete = $this->getView()->translate('Remove question');
        
        return '<div class="btn-group">
                    <a class="btn btn-mini btn-primary edit-question" href="'
                        .$urlEdit.'" title="'.$titleEdit.'">
                        <i class="icon-edit icon-white"></i>
                    </a>
                    <a class="btn btn-mini btn-primary delete-question" href="'
                        .$urlDelete.'" title="'.$titleDelete.'">
                        <i class="icon-remove icon-white"></i>
                    </a>
                </div>';
    }
    
}