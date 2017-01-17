<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest;


use Api\Dispatcher;
use Api\Service\Exception\NoTokenException;
use Api\Service\Exception\ServiceException;
use Api\Service\Response\Builder;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class DispatcherTest extends \PHPUnit_Framework_TestCase implements ServiceLocatorAwareInterface{

    /**
     * @var ServiceLocatorInterface
     */
    private $sm;

    protected function setUp() {
        $conf = require_once API_ROOT . "/config/service_manager.php";
        $config = $conf['service_manager'];
        $conf = new \Zend\ServiceManager\Config($config);
        $sm = new \Zend\ServiceManager\ServiceManager($conf);
        $this->setServiceLocator($sm);
    }

    protected function tearDown() {
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->sm = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->sm;
    }

    public function testDispatch(){
        $dispatcher = new \Api\Dispatcher();

        $response = $this->getMock("Api\\Service\\Response\\Builder");
        $response->expects($this->never())
            ->method("result");
        $exeption = null;
        try {
            $dispatcher->dispatch("test", "test", ['token' => 'some-token'], $response, $this->getServiceLocator());
        }catch(\Exception $e){
            $exeption = $e;
        }
        $this->assertInstanceOf("Api\\Service\\Exception\\ServiceException", $exeption);

        $dispatcher->registerServicesPath(TESTS_PATH . "tests/Api/Service/");


        $response = $this->getMock("Api\\Service\\Response\\Builder");
        $response->expects($this->once())
            ->method("setStatusCode")
            ->with($this->equalTo(Builder::STATUS_CODE_EMPTY_TOKEN));
        $response->expects($this->once())
            ->method("result");
        $dispatcher->dispatch("test1", "GetOffers", [], $response, $this->getServiceLocator());

        $response = $this->getMock("Api\\Service\\Response\\Builder");
        try {
            $dispatcher->dispatch("test1", "GetOffers", ['access_token' => 'some-token'], $response, $this->getServiceLocator());
        }catch(ServiceException $e) {
            $this->assertFalse(true);
        }
        $service = $dispatcher->getCalledService();
        $this->assertInstanceOf("ApiTest\\Service\\Test1Service", $service);


        $response = $this->getMock("Api\\Service\\Response\\Builder");
        $response->expects($this->once())
            ->method("setStatusCode")
            ->with($this->equalTo(Builder::STATUS_CODE_NOT_FOUND));

        $dispatcher->dispatch("not-service", "GetOffers", ['access_token' => 'some-token2'], $response, $this->getServiceLocator());
        $service = $dispatcher->getCalledService();
        $this->assertNull($service);


    }
}