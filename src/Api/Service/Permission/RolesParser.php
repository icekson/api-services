<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace Api\Service\Permission;


use Rbac\Role\HierarchicalRole;
use Rbac\Role\Role;
use Rbac\Role\RoleInterface;

class RolesParser implements Parser{

    const MAX_LEVEL_INHERITANCE = 3;
    private $config = array();


    /**
     * @param $confPath
     * @throws \InvalidArgumentException
     */
    public function __construct(array $config){
        $this->config = $config;
    }

    /**
     * @return RoleInterface[]
     */
    public function parse(){
        $res = array();
        if(is_array($this->config) && isset($this->config['roles'])){
            $roles = $this->config['roles'];
            if(is_array($roles)){
                foreach($roles as $name => $r){
                    $role = $this->parseRole($name, $r);
                    $res[] = $role;
                }
            }
        }
        return $res;
    }

    /**
     * @param $name
     * @param array $r
     * @return RoleInterface
     */
    private function parseRole($name, array $r, &$level = 0){
        $level++;

        $role = null;
        if($level < self::MAX_LEVEL_INHERITANCE && isset($r['extends'])){
            $role = new HierarchicalRole($name);
            if(is_array($r['extends'])){
                foreach($r['extends'] as $rr){
                    if(isset($this->config['roles'][$rr])){
                        $role->addChild($this->parseRole($rr, $this->config['roles'][$rr], $level));
                    }
                }
            }else{
                if(isset($this->config['roles'][$r['extends']])){
                    $role->addChild($this->parseRole($r['extends'], $this->config['roles'][$r['extends']], $level));
                }
            }
        }else{
            $role = new Role($name);
        }
        if(array_key_exists('permissions', $r)){
            foreach($r['permissions'] as $permission){
                $role->addPermission($permission);
            }
        }
        $level--;
        return $role;
    }

}