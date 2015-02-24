<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest\Service\Permission;


use Api\Service\Permission\RolesParser;

class RolesParserTest extends \PHPUnit_Framework_TestCase{

    public function testParser(){
        $conf = $this->getConfig();
        $parser = new RolesParser($conf);
        $roles = $parser->parse();

        $this->assertTrue(is_array($roles));
        $this->assertNotEquals(0, count($roles));

        foreach($roles as $role) {
            $this->assertInstanceOf("Rbac\\Role\\RoleInterface", $role);
            $this->assertArrayHasKey($role->getName(), $conf['roles']);
            if(isset($conf['roles'][$role->getName()]['extends'])){
                $this->assertInstanceOf("Rbac\\Role\\HierarchicalRole", $role);

                if($role instanceof \Rbac\Role\HierarchicalRole){
                    if(is_array($conf['roles'][$role->getName()]['extends'])) {
                        $this->assertNotEquals(0, count($role->getChildren()));
                    }else{
                        $this->assertEquals(1, count($role->getChildren()));
                        $this->assertArrayHasKey('permissions',$conf['roles'][$role->getName()]);

                        foreach($conf['roles'][$role->getName()]['permissions'] as $permission){
                            $this->assertTrue($role->hasPermission($permission));
                        }

                    }
                }

            }
        }
    }

    public function getConfig(){
        return array(
            'roles' => array(
                'developer' => array(
                    'permissions' => array(
                        'statistics.GetAdvertiserOffers',
                    )
                ),
                'publisher' => array(
                    'permissions' => array(
                        'statistics.GetPublisherOffers',
                    ),
                ),
                'admin' => array(
                    'extends' => array(
                        'publisher',
                        'developer'
                    )
                ),
                'test' => array(
                    'permissions' => array(
                        'test.GetGroupedData',
                    ),
                    'extends' => array(
                        'test2',
                    )
                ),
                'test2' => array(
                    'permissions' => array(
                        'test.GetGroupedData',
                    ),
                    'extends' => 'publisher'
                )
            )
        );
    }
}