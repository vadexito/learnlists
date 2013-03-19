<?php

namespace QuestionRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use Question\Entity\Round;
use Zend\Stdlib\DateTime;
class RoundRestController extends AbstractRestfulController
{
    /**
     * get all the rounds for a given member and a given list of questions
     * (listquest)
     * @return boolean|\Zend\View\Model\JsonModel
     */
    
    public function getList()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()){
            return false;
        }
        
        $listquestId = (int) $this->params()->fromQuery('listquestId',0);        
        
        $user = $this->zfcUserAuthentication()->getIdentity();
        $rep = $this->_getRepository();
        
        $qb = $rep->createQueryBuilder('r');
        $rounds = $qb   ->join('r.user','u')
                        ->join('r.listquest','l')
                        ->where('u.id = :userId')
                        ->andWhere('l.id = :listquestId')
                        ->setParameter('userId',$user->getId())
                        ->setParameter('listquestId',$listquestId)
                        ->getQuery()->getResult();
        
        $result = [];
        foreach ($rounds as $round){
            $result[] = $round->toArray();
        };
        
        return new JsonModel($result);
    }        
    
    public function get($id)
    {
//        $round = $this->_getRepository()->find($id);
//        
//        
//        return new JsonModel($round->toArray());        
    }
    
    public function create($data)
    {
        $round = new Round();
        $round->user = $this->zfcUserAuthentication()->getIdentity();
        $round->listquest = $this->_getRepository('Question\Entity\Listquest')
                                 ->find($data['listquestId']);
        
        //date are in UGT timezone
        $round->startDate = new DateTime($data['startDate']['date']);
        $round->endDate = new DateTime($data['endDate']['date']);
        
        $this->getEntityManager()->persist($round);
        
        try {
            
            $this->getEntityManager()->flush();
        } catch (Exception $e) {
            throw new \Exception('Doctrine creating failed');
        }
        
        return new JsonModel((array)$round->toArray());
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
        $round = $this->_getRepository()->find($id);
        if (!$round){
            return new JsonModel([]);
        }
        
        $this->getEntityManager()->remove($round);
        
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
    
    protected function _getRepository($entity = 'Question\Entity\Round')
    {
        return $this->getEntityManager()->getRepository($entity);
    }
}

