<?php

namespace ApiTest\Service;

use Api\BaseService;
use Api\Service\IdentityInterface;
use Api\Service\RemoteServiceInterface;
use Api\Service\SecurityOwnerPermissionInterface;
use Api\Service\SecurityServiceInterface;
use Api\Service\UserIdentity;
use Api\Service\Util\Properties;
use Api\Service\Annotation\ServiceAction;
use Api\Service\Annotation\Service;
use Api\Service\Response\Builder as ResponseBuilder;


/**
 * Class NewsService
 * @Service(name = "test2")
 */
class Test2Service extends BaseService implements RemoteServiceInterface, SecurityServiceInterface, SecurityOwnerPermissionInterface
{


    /**
     * @ServiceAction(name="GetTestData")
     */
    public function getOffers()
    {
        // TODO: Implement getOffers() method.
    }

    /**
     * @ServiceAction(name="GetSomeAnotherData")
     */
    public function getStatistics()
    {
        // TODO: Implement getStatistics() method.
    }

    /**
     * @param string $token
     * @return bool
     *
     */
    public function isPermitted($token)
    {
        return true;
    }

    /**
     * @param Properties $params
     * @return IdentityInterface|null
     *
     */
    public function getIdentity(Properties $params = null)
    {
        $identity = new UserIdentity();
        $identity->setRoles(array('publisher'));
        $identity->setId(1);
        return $identity;
    }

    /**
     * @return boolean
     */
    public function checkOwnPermission()
    {
         return true;
    }
}
