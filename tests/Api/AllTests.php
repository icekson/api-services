<?php

namespace ApiTest;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

define("API_ROOT" , realpath(TESTS_PATH . "../../api/") . "/");

class AllTests extends \PHPUnit_Framework_TestSuite {

    public static function suite() {
        $suite = new \ApiTest\AllTests('Api');

        $suite->addTestFile(TESTS_PATH . "tests/Api/Service/Util/PropertiesTest.php");
        $suite->addTestFile(TESTS_PATH . "tests/Api/DispatcherTest.php");
        $suite->addTestFile(TESTS_PATH . "tests/Api/Service/Permission/RolesParserTest.php");
        $suite->addTestFile(TESTS_PATH . "tests/Api/Service/Permission/CheckerTest.php");
        $suite->addTestFile(TESTS_PATH . "tests/Api/Service/Response/JsonBuilderTest.php");
        $suite->addTestFile(TESTS_PATH . "tests/Api/Service/ServiceFinderTest.php");
        $suite->addTestFile(TESTS_PATH . "tests/Api/Service/AnnotationsHelperTest.php");
        return $suite;
    }

    protected function setUp() {

    }

    protected function tearDown() {

    }


}