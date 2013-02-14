<?php

namespace Question\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class QuestionTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        
        return $resultSet;
    }

    public function fetchOneRandom()
    {
        $rowset = $this->tableGateway->select(function (Select $select) {
            $select->order(new Expression('RAND()'))->limit(1);
        });
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find any row");
        }
        
        return $row;
    }
    
    public function getQuestion($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveQuestion(Question $question)
    {
        $data = [
            'text'      => $question->text,
            'answer'    => $question->answer,
            'tip'       => $question->tip,
        ];

        $id = (int)$question->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue(); 
        } else {
            if ($this->getQuestion($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
        
        return $id; 
    }

    public function deleteQuestion($id)
    {
        $this->tableGateway->delete(['id' => $id]);
    }
}

