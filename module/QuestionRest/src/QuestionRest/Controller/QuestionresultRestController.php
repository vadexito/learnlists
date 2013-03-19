<?php

namespace QuestionRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Question\Entity\Questionresult;

class QuestionresultRestController extends AbstractRestfulController
{
    public function getList()
    {
        
    }        
        
    public function get($id)
    {
        
    }
    
    public function create($data)
    {
        $questionresult = new Questionresult();
        $questionresult->question = $this->_getRepository('Question\Entity\Question')
                                 ->find($data['questionId']);
        
        $questionresult->round = $this  ->_getRepository('Question\Entity\Round')
                                        ->find($data['roundId']);     
        
        $questionresult->answerType = $data['answerType'];
        
        $this->getEntityManager()->persist($questionresult);
        
        try {
            
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            throw new \Exception('Doctrine creating failed');
        }
        
        return new JsonModel((array)$questionresult->toArray());
    }
    
    public function update($id, $data)
    {
        
    }
    
    public function delete($id)
    {
        $this->getEntityManager()->remove($this->_getRepository()->find($id));
        
        try {
            
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            throw new \Exception('Doctrine deleting failed');
        }
        
        return new JsonModel([]);
    }
    
    public function getEntityManager()
    {
        return $this   ->getServiceLocator()
                        ->get('Doctrine\ORM\EntityManager');
    }
    
    protected function _getRepository($entity = 'Question\Entity\Questionresult')
    {
        return $this->getEntityManager()->getRepository($entity);
    }
    
    
}

