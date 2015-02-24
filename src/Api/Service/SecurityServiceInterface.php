<?php

/**
 * @author a.itsekson
 */

namespace Api\Service;


use Api\Service\Util\Properties;

interface SecurityServiceInterface {

    /**
     * @param string $token
     * @return bool
     *
     */
    public function isPermitted($token);

    /**
     * @param Properties $params
     * @return IdentityInterface|null
     *
     */
    public function getIdentity(Properties $params = null);
} 