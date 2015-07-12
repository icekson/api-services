# Description

Api services is developed in such way that it is fraework independed and can work with any PHP framework;

For dispatching is used route /api/v:version/:serviceName/:actionName, where

    * version : version of api (for example v1)
    * serviceName - service's name
    * serviceAction - action's name

Each service should implement \Api\Service\RemoteServiceInterface.
For determining what the service and action should be dispatched I use annotations
(Doctrine\Common\Annotations is used as engine for parsing annotations).

### Example:

```php
namespace Service;

use Api\Service\RemoteServiceInterface

/**
 * Class AdvertiserStatsService
 * @Service(name = "advertiser")
 */
class AdvertiserStatsService implements RemoteServiceInterface
{
    /**
     * @ServiceAction(name="GetOffers")
     * 
     *
     */
    public function getOffers()
    {
    }
}
```

For the convenience there is implemented abstract class Api\Service\BaseService which already implements interface RemoteServiceInterface and can be used as base class for services;

```php
namespace Service;

use Api\Service\BaseService

/**
 * Class AdvertiserStatsService
 * @Service(name = "advertiser")
 */
class AdvertiserStatsService extends BaseService
{
    /**
     * @ServiceAction(name="GetOffers")
     * 
     *
     */
    public function getOffers()
    {
    }
}
```

BTW, if you want to hide your service from public (add authorization by token, permissions checks etc.) you have to implement two additional interfaces Api\Service\SecurityServiceInterface and Api\Service\SecurityOwnerPermissionInterface

here is the full version of service example:

```php
namespace Service;

use Api\Service\BaseService;
use Api\Service\SecurityOwnerPermissionInterface;
use Api\Service\SecurityServiceInterface;
use Api\Service\IdentityInterface;

/**
 * Class AdvertiserStatsService
 * @Service(name = "advertiser")
 */
class AdvertiserStatsService extends BaseService implements SecurityServiceInterface, SecurityOwnerPermissionInterface
{
    /**
     * @ServiceAction(name="GetOffers")
     * 
     *
     */
    public function getOffers()
    {
    }

     public function isPermitted($token){}

     /**
     * @param Properties $params
     * @return IdentityInterface|null
     * @throw NoTokenException
     */
     public function getIdentity(Properties $params = null){}

     public function checkOwnPermission();
}
```

As you can see from the code, name of service is defined by according annotation @Service(name = "advertiser") and name of action - @ServiceAction(name="GetOffers") that's matches to url: /api/v1/advertiser/GetOffers

### Dispatching of requests

Workflow is the following:

  1 Both name of service and name of action are retrieved from routing
  2 The new instance of \Api\Dispatcher is created
  3 All the paths which contains implementations of services are need to be registered in dispatcher
  4 According service is called

Example:

```php

$version = "1";
$serviceName = "advertiser";
$serviceAction = "GetOffers";
$params = []; // some GET or POST params
$responseBuilder = new \Api\Service\Response\JsonBuilder(); // also it can be XmlBuilder or HtmlBuilder
$sm = new \Zend\ServiceManager\ServiceManager($conf);

$dispatcher = new \Api\Dispatcher();
$dispatcher->registerServicesPath(API_ROOT . "v".$version . "/services/Service/"); // this folder contains implementations of Services
$jsonResp = $dispatcher->dispatch($serviceName, $serviceAction, $params, $responseBuilder, $sm);

```

## Permissions setup

If your service implements Api\Service\SecurityServiceInterface then you should send access_token parameter with each request, you can keep all the tokens in DB or somewhere else. Validation of token you have to implement in method SecurityServiceInterface::isPermitted($token)

### Role based permissions

The list of roles and related permissions are placed in file api/config/permissions.php. Here we should describe the roles. If role is not defined explicity the access will be denied. The access can be defined for appropriate service/action (serviceName.serviceAction) as well as for all actions in the service (serviceName.*):

Also you can use inheritance of roles by using key 'extends' for any role and to list there parent roles, in such case this role will be extend all permissions from prent roles;

Example of api/config/permissions.php

```php
return array(
   'roles' => array(
       'developer' => array(
           'permissions' => array(
               'advertiser.*'
           ),
           'extends' => 'test'
       ),
       'affiliate' => array(
           'permissions' => array(
               'test.*',
           )
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
           )
       )
   )
);
```

### Setup of columns, filters, grouings

For any action we can use available columns, filters and groupings. Here is we use annotations as well

  @AcceptableColumns()
  @AcceptableFilters()
  @AceptableGroupings()

Few examples:

```php
 // 1 example

    /**
     * @ServiceAction(name="GetTerritoryStatistics")
     *
     * @AcceptableColumns({
     *      AdvertiserFilter::FIELD_OFFER_NAME,
     *      AdvertiserFilter::FIELD_TERRITORY,
     *      AdvertiserFilter::FIELD_CLICKS,
     *      AdvertiserFilter::FIELD_CONVERSIONS,
     *      AdvertiserFilter::FIELD_CR,
     *      AdvertiserFilter::FIELD_CR_PERSENTS,
     *      AdvertiserFilter::FIELD_SPENT,
     *      AdvertiserFilter::FIELD_CURRENCY
     * })
     *
     * @AcceptableFilters({
     *      AdvertiserFilter::DATE_FROM,
     *      AdvertiserFilter::DATE_TO,
     *      AdvertiserFilter::FIELD_OFFER_ID,
     *      AdvertiserFilter::FIELD_OFFER_DETAILS_ID
     *
     * })
     *
     * @AcceptableGroupings({
     *      AdvertiserFilter::FIELD_CPA
     * })
     */
    public function getTerritoryStatistics()
    {}

// 2 example

    /**
     * @ServiceAction(name="GetDailyStatistics")
     * @AcceptableColumns({
     *      AdvertiserFilter::FIELD_OFFER_NAME,
     *      AdvertiserFilter::FIELD_DATE,
     *      AdvertiserFilter::FIELD_CLICKS,
     *      AdvertiserFilter::FIELD_CONVERSIONS,
     *      AdvertiserFilter::FIELD_CR,
     *      AdvertiserFilter::FIELD_CR_PERSENTS
     * })
     * @AcceptableColumns(role = "admin", extendDefault = true, value = {
     *      AdvertiserFilter::FIELD_BD_MANAGER_ID
     * })
     *
     * @AcceptableFilters({
     *      AdvertiserFilter::DATE_FROM,
     *      AdvertiserFilter::DATE_TO,
     *      AdvertiserFilter::FIELD_OFFER_ID
     * })
     */
    public function getDailyStatistics() {}
```

As we can see, we can put the same annotations few times (@AcceptableColumns) in this case the most appropriate role of user will be choosen, by default will be selected the annotation whithout any role. As well you can use 'extendDefault = true' and all the columns will be extended from default annotation


#### AcceptableColumns

The columns can be defined as a string or array

  api/v1/someService/someAction?columns=all
  api/v1/someService/someAction?columns=name,id,date
  api/v1/someService/someAction?columns[]=name&columns[]=id&columns[]=date

If parameter columns will be empty then 'columns=all' will be used. If you give incorrect columns, you will get error with the list of available columns;

#### AcceptableFilters


  api/v1/someService/someAction?filters[date_from]=2011-01-01&filters[date_to]=2014-01-01


#### AcceptableGroupings

Format is the same as in columns

  api/v1/someService/someAction?group=name,id,date
  api/v1/someService/someAction?group[]=name&group[]=id&group[]=date