<?php
/**
 * @author a.itsekson
 * @date 10.02.2015
 * 
 */

namespace ApiTest\Service;


use Api\Service\AnnotationsHelper;

class AnnotationsHelperTest extends \PHPUnit_Framework_TestCase {

    public function testGetDefaultColumnsInstance(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test1");

        $columns = $helper->getAcceptableColumns();

        $this->assertTrue(is_array($columns));
        $this->assertTrue(in_array("def_column1", $columns));
        $this->assertTrue(in_array("def_column2", $columns));
    }

    public function testGetColumnsRelatedToRoleInstance(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test2");

        $columns = $helper->getAcceptableColumns();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(in_array("developer_column1", $columns));
        $this->assertTrue(in_array("developer_column2", $columns));
    }

    public function testGetColumnsEmpty(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test3");

        $columns = $helper->getAcceptableColumns();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(count($columns) === 0);
    }


    public function testGetDefaultFiltersInstance(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test4");

        $columns = $helper->getAcceptableFilters();

        $this->assertTrue(is_array($columns));
        $this->assertTrue(in_array("def_filter1", $columns));
        $this->assertTrue(in_array("def_filter2", $columns));
    }

    public function testGetFiltersRelatedToRoleInstance(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test5");

        $columns = $helper->getAcceptableFilters();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(in_array("developer_filter1", $columns));
        $this->assertTrue(in_array("developer_filter2", $columns));
    }

    public function testGetColumnsExtendDefault(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test6");

        $columns = $helper->getAcceptableColumns();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(count($columns) === 3);
        $this->assertTrue(in_array("def_col1",$columns));
        $this->assertTrue(in_array("admin_col1",$columns));
    }

    public function testGetFiltersExtendDefault(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test7");

        $columns = $helper->getAcceptableFilters();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(count($columns) === 3);
        $this->assertTrue(in_array("def_filter1",$columns));
        $this->assertTrue(in_array("admin_filter1",$columns));
    }


    public function testGetDefaultGroupingInstance(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test1");

        $columns = $helper->getAcceptableGroupings();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(in_array("group1", $columns));
        $this->assertTrue(in_array("group2", $columns));
    }

    public function testGetGroupingsRelatedToRoleInstance(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test2");

        $columns = $helper->getAcceptableGroupings();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(in_array("developer_group1", $columns));
        $this->assertTrue(in_array("developer_group2", $columns));
    }

    public function testGetGroupingsEmpty(){
        $respBuilder = $this->getMock("Api\\Service\\Response\\Builder");
        $params = $this->getMock("Api\\Service\\Util\\Properties");
        $sm = $this->getMock("Zend\\ServiceManager\\ServiceLocatorInterface");

        $service = new \ApiTest\Service\TestAcceptableAnnotationsService($sm, $params, $respBuilder);
        $helper = new AnnotationsHelper($service, "test3");

        $columns = $helper->getAcceptableGroupings();
        $this->assertTrue(is_array($columns));
        $this->assertTrue(count($columns) === 0);
    }

}