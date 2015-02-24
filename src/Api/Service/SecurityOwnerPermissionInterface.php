<?php
/**
 * @author a.itsekson
 * @date 09.02.2015
 * 
 */

namespace Api\Service;


interface SecurityOwnerPermissionInterface {

    /**
     * @return boolean
     */
    public function checkOwnPermission();

}