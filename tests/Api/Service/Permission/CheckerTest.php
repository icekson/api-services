<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest\Service\Permission;


use Api\Service\Permission\Checker;
use Rbac\Role\HierarchicalRole;
use Rbac\Role\Role;
use Rbac\Role\RoleInterface;

class CheckerTest extends \PHPUnit_Framework_TestCase{


    /**
     * @param $serviceName
     * @param $actionName
     * @dataProvider actionsGenerator
     */
    public function testCheckSimpleRoleAccess($serviceName, $actionName){
        $role = $this->getSimpleRole();
        $checker = new Checker();
        $this->assertTrue($checker->checkPermission($serviceName, $actionName, $role));
    }

    /**
     * @param $serviceName
     * @param $actionName
     * @dataProvider actionsGenerator
     */
    public function testCheckRoleAccessNotPermitted($serviceName, $actionName){
        $role = $this->getSimpleRole2();
        $checker = new Checker();
        $this->assertFalse($checker->checkPermission($serviceName, $actionName, $role));
    }
    /**
     * @param $serviceName
     * @param $actionName
     * @dataProvider actionsGenerator
     */
    public function testCheckExtendedRoleAccess($serviceName, $actionName){
        $role = $this->getExtendRole();
        $checker = new Checker();
        if($serviceName == 'testService' && $actionName == 'testAction2'){
            $this->assertFalse($checker->checkPermission($serviceName, $actionName, $role));
        }else {
            $this->assertTrue($checker->checkPermission($serviceName, $actionName, $role));
        }
    }

    /**
     * @param $serviceName
     * @param $actionName
     * @dataProvider actionsGenerator
     */
    public function testCheckExtendedDeepRoleAccess($serviceName, $actionName){
        $role = $this->getDeepExtendRole();
        $checker = new Checker();
        if($serviceName == 'testService' && $actionName == 'testAction2'){
            $this->assertFalse($checker->checkPermission($serviceName, $actionName, $role));
        }else {
            $this->assertTrue($checker->checkPermission($serviceName, $actionName, $role));
        }
    }

    /**
     * @return RoleInterface
     *
     */
    public function getSimpleRole(){
        $role = new Role("testRole");
        $role->addPermission("testService.testAction");
        $role->addPermission("testService.testAction2");
        $role->addPermission("testService3.testAction3");
        $role->addPermission("testService4.*");
        $role->addPermission("testService4.sssssss");
        $role->addPermission("testService5.testAction5");
        return $role;
    }

    /**
     * @return RoleInterface
     *
     */
    public function getSimpleRole2(){
        $role = new Role("testRole2");
        $role->addPermission("someService.someAction");
        $role->addPermission("someService2.*");
        return $role;
    }

    /**
     * @return RoleInterface
     *
     */
    public function getExtendRole(){
        $role = new HierarchicalRole("testParentRole");
        $role->addPermission("testService.testAction");

        $child1 = new Role('childRole1');
        $child1->addPermission("testService3.testAction3");
        $role->addChild($child1);

        $child2 = new Role('childRole2');
        $child2->addPermission("testService4.*");
        $role->addChild($child2);

        $child3 = new Role('childRole3');
        $child3->addPermission("testService5.testAction5");
        $role->addChild($child3);

        return $role;
    }

    /**
     * @return RoleInterface
     *
     */
    public function getDeepExtendRole(){
        $role = new HierarchicalRole("testParentRole");
        $role->addPermission("testService.testAction");

        $child1 = new HierarchicalRole('childRole1');
        $child1->addPermission("testService3.testAction3");
        $ch2 = new Role('deepRole');
        $ch2->addPermission('testService5.testAction5');
        $child1->addChild($ch2);
        $role->addChild($child1);

        $child2 = new Role('childRole2');
        $child2->addPermission("testService4.*");
        $role->addChild($child2);

        return $role;
    }

    public function actionsGenerator(){
        return [
            ["testService","testAction"],
            ["testService","testAction2"],
            ["testService3","testAction3"],
            ["testService4","testSomeCustom"],
            ["testService5","testAction5"],
        ];
    }


}