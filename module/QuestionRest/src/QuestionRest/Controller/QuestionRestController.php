<?php

namespace QuestionRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Question\Model\Question;
use Question\Form\QuestionForm;
use Question\Model\QuestionTable;
use Zend\View\Model\JsonModel;

class QuestionRestController extends AbstractRestfulController
{
    protected $questionTable;
    
    public function getList()
    {
        $results = $this->getQuestionTable()->fetchAll();
        $data = [];
        foreach($results as $result) {
            $data[] = $result;
        }
 
    return new JsonModel((array)$data);
    }        
        
    public function get($id)
    {
        $question = $this->getQuestionTable()->getQuestion($id);
        
        return new JsonModel((array)$question);
    }
    
    public function create($data)
    {
//        $form = new QuestionForm();
//        $question = new Question();
//        $form->setInputFilter($question->getInputFilter());
//        $form->setData($data);
//        if ($form->isValid()) {
//            $question->exchangeArray($form->getData());
//            $id = $this->getQuestionTable()->saveQuestion($question);
//        }

        return new JsonModel((array)$this->get($id));
    }
    
    public function update($id, $data)
    {
//        $data['id'] = $id;
//        $question = $this->getQuestionTable()->getQuestion($id);
//        $form  = new QuestionForm();
//        $form->bind($question);
//        $form->setInputFilter($question->getInputFilter());
//        $form->setData($data);
//        if ($form->isValid()) {
//            $id = $this->getQuestionTable()->saveQuestion($form->getData());
//        }
//
//        return new JsonModel((array)$this->get($id));
    }
    
    public function delete($id)
    {
//        $this->getQuestionTable()->deleteQuestion($id); 
//        return new JsonModel(['data' => 'deleted']);
    }
}

