<?php

namespace QuestionRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ListquestRestController extends AbstractRestfulController
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
        $list = $this   ->getServiceLocator()
                        ->get('Doctrine\ORM\EntityManager')
                        ->getRepository('Question\Entity\Listquest')
                        ->find($id);
                
        return new JsonModel($list->toArray());
    }
    
    public function create($data)
    {
        $form = new QuestionForm();
        $question = new Question();
        $form->setInputFilter($question->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $question->exchangeArray($form->getData());
            $id = $this->getQuestionTable()->saveQuestion($question);
        }

        return new JsonModel((array)$this->get($id));
    }
    
    public function update($id, $data)
    {
        $data['id'] = $id;
        $question = $this->getQuestionTable()->getQuestion($id);
        $form  = new QuestionForm();
        $form->bind($question);
        $form->setInputFilter($question->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $id = $this->getQuestionTable()->saveQuestion($form->getData());
        }

        return new JsonModel((array)$this->get($id));
    }
    
    public function delete($id)
    {
        $this->getQuestionTable()->deleteQuestion($id); 
        return new JsonModel(['data' => 'deleted']);
    }
    
    public function getQuestionTable()
    {
        if (!$this->questionTable) {
            $sm = $this->getServiceLocator();
            $this->questionTable = $sm->get('Question\Model\QuestionTable');
        }
        return $this->questionTable;
    }
}

