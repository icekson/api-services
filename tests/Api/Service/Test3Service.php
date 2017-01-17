<?php

namespace ApiTest\Service;

use Api\BaseService;
use Api\Service\AnnotationsHelper;
use Api\Service\IdentityInterface;
use Api\Service\RemoteServiceInterface;
use Api\Service\SecurityServiceInterface;
use Api\Service\UserIdentity;
use Api\Service\Util\Properties;
use Api\Service\Annotation\ServiceAction;
use Api\Service\Annotation\Service;
use Api\Service\Annotation\Description;
use Api\Service\Annotation\Input;
use Api\Service\Annotation\Deprecated;
use Api\Service\Annotation\TestApi;
use Api\Service\Response\Builder as ResponseBuilder;



/**
 * Class NewsService
 * @Service(name = "test3")
 */
class Test3Service extends BaseService
{

    protected function init(){}
    /**
     * @ServiceAction(name="test-api1")
     * @Description("Some description 1")
     * @Input(name="testParam1", type="int")
     * @Input(name="testParam2", type="string", required="true")
     * @Input(name="testParam3", type="array", acceptableValues="test1,test2,test3")
     */
    public function getOffers()
    {
        // TODO: Implement getOffers() method.
    }

    /**
     * @ServiceAction(name="test-api2")
     * @Input(name="testParam1", type="array", acceptableValues={"test1","test2","test3"})
     */
    public function getStatistics()
    {
        // TODO: Implement getStatistics() method.
    }


    /**
     * @ServiceAction(name="test-api3")
     * @TestApi
     * @Input(name="testParam1", type="array", acceptableValues={"test1","test2","test3"})
     */
    public function testApi()
    {
        // TODO: Implement getStatistics() method.
    }


    /**
     * @ServiceAction(name="test-api4")
     * @Deprecated
     * @Input(name="testParam1", type="array", acceptableValues={"test1","test2","test3"})
     */
    public function deprecatedApi()
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
        $identity->setRoles(array('developer','admin'));
        $identity->setId(1);
        return $identity;
    }

}
