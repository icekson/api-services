<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace Api\Service;


interface IdentityInterface {
    public function getId();
    public function setId($id);
    public function getRoles();
    public function setRoles($roles);
}