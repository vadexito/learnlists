<?php
namespace QuestionTest\Model;

use Question\Model\QuestionTable;
use Question\Model\Question;
use Zend\Db\ResultSet\ResultSet;
use PHPUnit_Framework_TestCase;

class QuestionTableTest extends PHPUnit_Framework_TestCase
{
    public function testQuestionInitialState()
    {
        $question = new Question();

        $this->assertNull($question->artist, '"artist" should initially be null');
        $this->assertNull($question->id, '"id" should initially be null');
        $this->assertNull($question->title, '"title" should initially be null');
    }

    public function testExchangeArraySetsPropertiesCorrectly()
    {
        $question = new Question();
        $data  = array('artist' => 'some artist',
                       'id'     => 123,
                       'title'  => 'some title');

        $question->exchangeArray($data);

        $this->assertSame($data['artist'], $question->artist, '"artist" was not set correctly');
        $this->assertSame($data['id'], $question->id, '"id" was not set correctly');
        $this->assertSame($data['title'], $question->title, '"title" was not set correctly');
    }

    public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent()
    {
        $question = new Question();

        $question->exchangeArray(array('artist' => 'some artist',
                                    'id'     => 123,
                                    'title'  => 'some title'));
        $question->exchangeArray(array());

        $this->assertNull($question->artist, '"artist" should have defaulted to null');
        $this->assertNull($question->id, '"id" should have defaulted to null');
        $this->assertNull($question->title, '"title" should have defaulted to null');
    }
    
    public function testFetchAllReturnsAllQuestions()
    {
        $resultSet        = new ResultSet();
        $mockTableGateway = $this->getMock('Zend\Db\TableGateway\TableGateway',
                                           array('select'), array(), '', false);
        $mockTableGateway->expects($this->once())
                         ->method('select')
                         ->with()
                         ->will($this->returnValue($resultSet));

        $questionTable = new QuestionTable($mockTableGateway);

        $this->assertSame($resultSet, $questionTable->fetchAll());
    }
}