<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest\Service;


use Api\Service\ServiceFinder;

class ServiceFinderTest extends \PHPUnit_Framework_TestCase{
    public function testFindServices(){
        $finder = new ServiceFinder();
        $dir = new \DirectoryIterator(TESTS_PATH . "Api/Service/");
        $res = $finder->scanFolder($dir);

        $this->assertCount(4, $res);

        foreach($res as $class){
            $this->assertInstanceOf("\\ReflectionClass", $class);
            $this->assertTrue($class->implementsInterface("Api\\Service\\RemoteServiceInterface"));
        }
    }
}