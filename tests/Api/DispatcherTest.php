<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace ApiTest;


use Api\Container\ContainerAwareInterface;
use Api\Container\ContainerImpl;
use Api\Dispatcher;
use Api\Service\Exception\NoTokenException;
use Api\Service\Exception\ServiceException;
use Api\Service\Response\Builder;
use Psr\Container\ContainerInterface;


class DispatcherTest extends \PHPUnit\Framework\TestCase implements ContainerAwareInterface {

    /**
     * @var ContainerInterface
     */
    private $sm;

    protected function setUp() {
        $conf = require_once API_ROOT . "tests/service_manager.php";
        $config = $conf['service_manager'];
        $sm = new ContainerImpl($config);
        $this->setContainer($sm);
    }

    protected function tearDown() {
    }

    /**
     * Set service locator
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->sm = $container;
    }

    /**
     * Get service locator
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->sm;
    }

    public function testDispatch(){
        $dispatcher = new \Api\Dispatcher();

        $response = $this->getMockBuilder("Api\\Service\\Response\\Builder")->getMock();
        $response->expects($this->never())
            ->method("result");
        $exeption = null;
        try {
            $dispatcher->dispatch("test", "test", ['token' => 'some-token'], $response, $this->getContainer());
        }catch(\Exception $e){
            $exeption = $e;
        }
        $this->assertInstanceOf("Api\\Service\\Exception\\ServiceException", $exeption);

        $dispatcher->registerServicesPath(TESTS_PATH . "tests/Api/Service/");


        $response = $this->getMockBuilder("Api\\Service\\Response\\Builder")->getMock();
        $response->expects($this->once())
            ->method("setStatusCode")
            ->with($this->equalTo(Builder::STATUS_CODE_EMPTY_TOKEN));
        $response->expects($this->once())
            ->method("result");
        $dispatcher->dispatch("test1", "GetOffers", [], $response, $this->getContainer());

        $response = $this->getMockBuilder("Api\\Service\\Response\\Builder")->getMock();
        try {
            $dispatcher->dispatch("test1", "GetOffers", ['access_token' => 'some-token'], $response, $this->getContainer());
        }catch(ServiceException $e) {
            $this->assertFalse(true);
        }
        $service = $dispatcher->getCalledService();
        $this->assertInstanceOf("ApiTest\\Service\\Test1Service", $service);


        $response = $this->getMockBuilder("Api\\Service\\Response\\Builder")->getMock();
        $response->expects($this->once())
            ->method("setStatusCode")
            ->with($this->equalTo(Builder::STATUS_CODE_NOT_FOUND));

        $dispatcher->dispatch("not-service", "GetOffers", ['access_token' => 'some-token2'], $response, $this->getContainer());
        $service = $dispatcher->getCalledService();
        $this->assertNull($service);


    }
}