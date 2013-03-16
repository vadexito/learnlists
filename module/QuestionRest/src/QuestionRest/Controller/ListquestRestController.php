<?php

namespace QuestionRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class ListquestRestController extends AbstractRestfulController
{
    protected $questionTable;
    
    public function getList()
    {
//        $results = $this->getQuestionTable()->fetchAll();
//        $data = [];
//        foreach($results as $result) {
//            $data[] = $result;
//        }
 
//    return new JsonModel((array)$data);
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
        var_dump($data);
//        $round = new Round();
//        $round->user = $this->zfcUserAuthentication()->getIdentity();
//        $round->listquest = $this->_getRepository('Question\Entity\Listquest')
//                                 ->find($data['listquestId']);
//        
//        //date are in UGT timezone
//        $round->startDate = new DateTime($data['startDate']);
//        $round->endDate = new DateTime($data['endDate']);
//        
//        $this->getEntityManager()->persist($round);
//        
//        try {
//            
//            $this->getEntityManager()->flush();
//        } catch (Exception $e) {
//            throw new \Exception('Doctrine creating failed');
//        }
//        
//        return new JsonModel((array)$round->toArray());
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

