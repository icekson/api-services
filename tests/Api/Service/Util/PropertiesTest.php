<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest\Service\Util;



use Api\Service\Util\Properties;

class PropertiesTest extends \PHPUnit\Framework\TestCase{

    public function testGetSetProperty(){
        $props = new Properties([]);
        $props->put("test", 4);

        $this->assertEquals(4, $props->get("test"));
    }

    public function testGetPropertyGivenByConstructor(){
        $props = new Properties(["someData" => "data"]);

        $this->assertEquals("data", $props->get("someData"));
    }

    public function testGetPropertyWithDefaultValue(){
        $props = new Properties(["someData" => "data"]);
        $this->assertEquals("data", $props->get("someData", "default1"));
        $this->assertEquals("default2", $props->get("test", "default2"));
    }
}