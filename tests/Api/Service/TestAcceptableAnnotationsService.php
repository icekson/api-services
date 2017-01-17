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
use Api\Service\Response\Builder as ResponseBuilder;
use Api\Service\Annotation\AcceptableColumns;
use Api\Service\Annotation\AcceptableFilters;
use Api\Service\Annotation\AcceptableGroupings;


/**
 * Class NewsService
 * @Service(name = "test_accept")
 */
class TestAcceptableAnnotationsService extends BaseService implements SecurityServiceInterface
{

    protected function init(){}

    /**
     * @ServiceAction(name="testActionDefault")
     * @AcceptableColumns({
     *    "def_column1",
     *    "def_column2"
     * })
     *
     * @AcceptableGroupings({
     *    "group1",
     *    "group2"
     * })
     */
    public function test1()
    {

    }

    /**
     * @ServiceAction(name="testActionDefaultAndRole")
     *
     * @AcceptableColumns({
     *    "def_column1",
     *    "def_column2"
     * })
     * @AcceptableColumns(role = "developer", value = {
     *    "developer_column1",
     *    "developer_column2"
     * })
     *
     * @AcceptableGroupings({
     *    "def_group1",
     *    "def_group2"
     * })
     * @AcceptableGroupings(role = "developer", value = {
     *    "developer_group1",
     *    "developer_group2"
     * })
     */
    public function test2()
    {

    }


    /**
     * @ServiceAction(name="testActionDefaultAndRole2")
     */
    public function test3()
    {

    }


    /**
     * @ServiceAction(name="testActionDefaultAndRole")
     *
     * @AcceptableFilters({
     *    "def_filter1",
     *    "def_filter2"
     * })
     */
    public function test4()
    {

    }

    /**
     * @ServiceAction(name="testActionDefaultAndRole")
     *
     * @AcceptableFilters({
     *    "def_filter1",
     *    "def_filter2"
     * })
     * @AcceptableFilters(role = "developer", value = {
     *    "developer_filter1",
     *    "developer_filter2"
     * })
     */
    public function test5()
    {

    }



    /**
     * @ServiceAction(name="testActionDefaultAndRoleExtend")
     *
     * @AcceptableColumns({
     *    "def_col1",
     *    "def_col2"
     * })
     * @AcceptableColumns(role = "developer", extendDefault = true, value = {
     *    "admin_col1"
     * })
     */
    public function test6()
    {

    }

    /**
     * @ServiceAction(name="testActionDefaultAndRoleExtend")
     *
     * @AcceptableFilters({
     *    "def_filter1",
     *    "def_filter2"
     * })
     * @AcceptableFilters(role = "developer", extendDefault = true, value = {
     *    "admin_filter1"
     * })
     */
    public function test7()
    {

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
