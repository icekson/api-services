<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest\Service\Util;



use Api\Service\Util\ApiDocumentator;
use Api\Service\Util\Properties;

class ApiDocumentatorTest extends \PHPUnit_Framework_TestCase{

   public function testGenerateDocs()
   {
       $dispatcher = new \Api\Dispatcher();
       $dispatcher->registerServicesPath(TESTS_PATH . "Api/Service/");

       $documentator = new ApiDocumentator($dispatcher);

       $data = $documentator->generateDocs();
       $this->assertTrue(is_array($data));


       $this->assertCount(4, $data);
       $this->assertTrue(isset($data['test3']));
       $this->assertCount(2, $data['test3']);

       $apis = $data['test3'];
       foreach ($apis as $api) {
           $this->assertTrue(isset($api['name']));
           $this->assertTrue(isset($api['url']));
           $this->assertTrue(isset($api['description']));
           $this->assertTrue(isset($api['inputParams']));
           $this->assertTrue(isset($api['type']));
           $this->assertEquals("json", $api['type']);

           foreach ($api['inputParams'] as $inputParam) {
               $this->assertTrue(isset($inputParam['name']));
               $this->assertTrue(isset($inputParam['type']));
               $this->assertTrue(in_array($inputParam['type'], ["int", "string", "array"]));
               $this->assertTrue(isset($inputParam['acceptableValues']));
               $this->assertTrue(is_array($inputParam['acceptableValues']));
               $this->assertTrue(isset($inputParam['required']));
               $this->assertTrue(is_bool($inputParam['required']));

           }

           if($api['name'] === "test-api1"){
               $this->assertEquals("Some description 1", $api['description']);
               $this->assertCount(3, $api['inputParams']);
               foreach ($api['inputParams'] as $i => $inputParam) {
                   if($i == 0){
                       $this->assertEquals("testParam1", $inputParam["name"]);
                       $this->assertEquals("int", $inputParam["type"]);
                   }else  if($i == 1){
                       $this->assertEquals("testParam2", $inputParam["name"]);
                       $this->assertEquals("string", $inputParam["type"]);
                       $this->assertTrue($inputParam['required']);
                   }else if($i == 2){
                       $this->assertEquals("testParam3", $inputParam["name"]);
                       $this->assertEquals("array", $inputParam["type"]);
                       $this->assertCount(3, $inputParam["acceptableValues"]);
                   }
               }
           }
           if($api['name'] === "test-api2"){
               $this->assertCount(1, $api['inputParams']);
               $this->assertCount(3, $api['inputParams'][0]["acceptableValues"]);
           }
       }


   }
}