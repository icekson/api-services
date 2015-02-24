<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest\Service\Response;


use Api\Service\Response\Builder;
use Api\Service\Response\JsonBuilder;

class JsonBuilderTest extends \PHPUnit_Framework_TestCase{

    public function testSetData(){
        $builder = new JsonBuilder();
        $builder->setData("data");

        $this->assertEquals("data", $builder->getData());

        $data = array(
            'some1' => "asdasdasd",
            'some2' => 3,
            'some3' => (object)array('asdas' => 'asdasd')
        );
        $builder->setData($data);
        $this->assertEquals($data, $builder->getData());
        $this->assertArrayHasKey('some2', $builder->getData());
    }

    public function testResult(){
        $builder = new JsonBuilder();
        $data = array(
            'some1' => "test",
            'some2' => 3,
        );
        $builder->setData($data);

        $test = '{"status":"success","success":true,"message":"","data":{"some1":"test","some2":3}}';
        $this->assertEquals($test, $builder->result());
        $decoded = json_decode($builder->result());

        $this->assertInstanceOf("\\stdClass", $decoded);
        $this->assertEquals((object)$data, $decoded->data);

    }

    public function testSetError(){
        $builder = new JsonBuilder();
        $test = '{"status":"success","success":true,"message":"","data":""}';

        $this->assertEquals($test, $builder->result());
        $builder->setError("some error");
        $test = '{"status":"error","success":false,"message":"some error","data":""}';
        $this->assertEquals($test, $builder->result());
        $this->assertTrue($builder->isError());
        $this->assertEquals(Builder::STATUS_CODE_ERROR, $builder->getStatusCode());
    }

    public function testSetCustomRootDataElement(){
        $builder = new JsonBuilder();
        $builder->setRootElementName("anotherData");
        $test = '{"status":"success","success":true,"message":"","anotherData":1}';
        $builder->setData(1);

        $this->assertEquals($test, $builder->result());

    }

    public function testAddCustomElements(){
        $builder = new JsonBuilder();
        $builder->addCustomElement("count", 10);
        $this->assertRegExp("/\"count\"\s?:\s?10/", $builder->result());
    }

    public function testSetMessage(){
        $builder = new JsonBuilder();
        $builder->setMessages("test message");
        $this->assertRegExp("/\"message\"\s?:\s?\"test\\smessage\"/", $builder->result());

        $messages = array("test1", "test2");
        $builder->setMessages($messages);
        $this->assertRegExp("/\"message\"\s?:\s?\"test1;.*?test2\"/", $builder->result());
    }
}