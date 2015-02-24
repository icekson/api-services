<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace Api\Service\Permission;


use Rbac\Rbac;
use Rbac\Role\Role;
use Rbac\Role\RoleInterface;
use Rbac\Traversal\Strategy\RecursiveRoleIteratorStrategy;

class Checker {
    /**
     * @var Rbac
     */
    private $rbac = null;

    public function __construct(){
        $this->rbac = new Rbac(new RecursiveRoleIteratorStrategy());
    }

    /**
     * @param $serviceName
     * @param $serviceAction
     * @param RoleInterface $role
     * @return boolean
     */
    public function checkPermission($serviceName, $serviceAction, $role){

        $res = $this->rbac->isGranted($role, $serviceName . ".*");
        if($res){
            return $res;
        }
        return $res = $this->rbac->isGranted($role, $serviceName . "." . $serviceAction);
    }

}