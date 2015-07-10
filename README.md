==Описание==
Для реализации RESTfull API создана отдельная точка входа в проекте http://mmotraffic.com/api, сама точка входа реализована с помощью микро фреймворка Slim.
==Описание архитектуры==

=== Сервисы ===
Api сервисы спроектированы таким образом, что являются фреймворконезависимыми, т.е. в принципе, их можно поднять на любом php фреймворке.

Для диспетчерезации запросов используется настроенный роут типа /api/v:version/:serviceName/:actionName, где 

    - version : версия api (на данный момент реализована v1)
    - serviceName - имя сервиса
    - serviceAction - имя экшена

Реализации сервисов лежат в папке /api/v1/services/Service. 
Каждый сервис должен реализовывать интерфейс \Api\Service\RemoteServiceInterface.
Соответствие имени сервиса и экшена с теми что заданы в запросе сопоставляются с помощью аннотаций (для парсинга используется Doctrine\Common\Annotations).

==== Пример Сервиса:====

<syntaxhighlight lang="php">
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
</syntaxhighlight>

Для удобства, создан абстрактный класс Api\Service\BaseService который реализует интерфейс RemoteServiceInterface, от которого можно наследовать конечный сервис


<syntaxhighlight lang="php">
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
</syntaxhighlight>


Но кроме этого, для того что бы сервис  был приватным (авторизация по токену и проверка пермиссий по роли), необходимо также реализовать 2 дополнительных интерфейса Api\Service\SecurityServiceInterface, и Api\Service\SecurityOwnerPermissionInterface. Таким образом вот выглядит полная версия сервиса:

<syntaxhighlight lang="php">
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
</syntaxhighlight>

Как видно из кода имя сервиса задается аннотацией @Service(name = "advertiser"), а имя экшена @ServiceAction(name="GetOffers") это соответствует url-у : /api/v1/advertiser/GetOffers.

=== Диспетчерезация запросов ===

Workflow следующий:

  1 Из роута вытягиваются имя сервиса и имя экшена
  2 Создается экземпляр Диспетчера \Api\Dispatcher
  3 Регистрируются пути с классами конечных сервисов
  4 Дергается сервис

Пример кода:


<syntaxhighlight lang="php">
$version = "1";
$serviceName = "advertiser";
$serviceAction = "GetOffers";
$params = []; // some GET or POST params
$responseBuilder = new \Api\Service\Response\JsonBuilder(); // also it can be XmlBuilder or HtmlBuilder
$sm = new \Zend\ServiceManager\ServiceManager($conf);

$dispatcher = new \Api\Dispatcher();
$dispatcher->registerServicesPath(API_ROOT . "v".$version . "/services/Service/"); // this folder contains implementations of Services
$jsonResp = $dispatcher->dispatch($serviceName, $serviceAction, $params, $responseBuilder, $sm);


</syntaxhighlight>


== Настройка доступа ==
Если сервис реализует интерфейс Api\Service\SecurityServiceInterface, то с каждым запросом необходимо передавать параметр access_token, токены хранятся в БД, в таблице api_access_tokens. Собственно проверка валидности токена реализуется в методе SecurityServiceInterface::isPermitted($token).

=== Настройка доступа с использованием ролей ===
Список ролей и разрешений для них находятся в конфиге api/config/permissions.php. Тут описываются роли (все роли должны соответствовать ролям и з таблицы users.roles). Если явно не разрешить доступ к сервису, то по умолчанию доступ будет запрещен. Доступ можно задать как на отдельный экшен (serviceName.serviceAction) так и на весь сервис в целом (serviceName.*):

Также поддерживается наследование ролей, если задать параметр extends для роли и указать в нем перечень ролей в виде массива или одной роли в виде строки, то данная роль будет наследовать все пермисии родительских ролей


Пример api/config/permissions.php
<syntaxhighlight lang="php">
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
</syntaxhighlight>

=== Настройка колонок, фильтров, группировок ===

Для каждого экшена в сервисе можно настроить допустимые колонки, фильтры и группировки. Это осуществляется с помощью аннотаций
  @AcceptableColumns()
  @AcceptableFilters()
  @AceptableGroupings()

Вот пару примеров:

<syntaxhighlight lang="php">
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
</syntaxhighlight>

Как видно из кода, можно задавать несколько одинаковых аннотаций (см. @AcceptableColumns), при этом будет выбрана та аннотация которая более соответствует роли текущего пользователя (параметр role в аннотации). По умолчанию берется аннотация без указанной роли (дефолтная), если указана аннотация для конкретной роли, например 'admin' и у текущего пользователя имеется эта роль, то будет выбрана эта аннотация вместо дефотной. Также, если указать параметр extendDefault = true, то перечень колонок будет расширен из дефолтной аннотации.

==== AcceptableColumns ====
Колонки можно передавать как в виде строки с запятыми так и массивом

  api/v1/someService/someAction?columns=all
  api/v1/someService/someAction?columns=name,id,date
  api/v1/someService/someAction?columns[]=name&columns[]=id&columns[]=date

Если ничего не передать, то по умолчанию будет выбран columns=all, Если передать не допустимые колонки, то будет возвращена ошибка с перечнем допустимых колонок.


==== AcceptableFilters ====
Фильтры передаются в виде ассоциативного массива:

  api/v1/someService/someAction?filters[date_from]=2011-01-01&filters[date_to]=2014-01-01

Если ничего не передать, то по умолчанию будет использованы фильтры date_from и date_to Month To Date. Если передать не допустимые фильтры, то будет возвращена ошибка с перечнем допустимых колонок. Фильтры также как и колонки можно расширять (см. AcceptableColumns)


==== AcceptableGroupings ====
Формат такой же как и для columns
  api/v1/someService/someAction?group=name,id,date
  api/v1/someService/someAction?group[]=name&group[]=id&group[]=date

Если ничего не передать, то по умолчанию будет использованы дефолтные группировки (например для GetDailyStatistics используется группировка по date).
