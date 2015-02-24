<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace Api\Service;


class UserIdentity implements IdentityInterface {

    private $id = -1;

    /**
     * @var array
     */
    private $roles = array();

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param $roles
     */
    public function setRoles($roles)
    {
        if(is_array($roles)){
            $this->roles = $roles;
        }else{
            $this->roles = explode(",", $roles);
        }
    }
}