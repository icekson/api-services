<?php

namespace Api;

use Api\Service\AnnotationsHelper;
use Api\Service\Exception\ServiceException;
use Api\Service\RemoteServiceInterface;
use Api\Service\Response\Builder as ResponseBuilder;
use Api\Service\Util\Properties;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


abstract class BaseService implements ServiceLocatorAwareInterface, RemoteServiceInterface{

    public function __construct(ServiceLocatorInterface $sm = null, Properties $params = null, ResponseBuilder $builder = null){
        if($builder !== null) {
            $this->setResponseBuilder($builder);
        }
        if($params !== null) {
            $this->setProperties($params);
        }
        if($sm !== null) {
            $this->setServiceLocator($sm);
        }
        $this->init();
    }

    abstract protected function init();

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceManager;

    /**
     * @var Properties
     */
    protected $params = null;

    /**
     * @var ResponseBuilder
     */
    protected $response = null;


    private $identity = null;


    /**
     * @return ResponseBuilder
     */
    public function getResponseBuilder(){
        return $this->response;
    }


    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceManager = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceManager;
    }

    /**
     * @param Properties $props
     * @return mixed
     */
    public function setProperties(Properties $props)
    {
        $this->params = $props;
    }

    /**
     * @return Properties
     */
    public function getProperties()
    {
        return $this->params;
    }

    /**
     * @param ResponseBuilder $builder
     * @return mixed
     */
    public function setResponseBuilder(ResponseBuilder $builder)
    {
        $this->response = $builder;
    }


    /**
     * @return AnnotationsHelper
     */
    public function getAnnotationsHelper()
    {
        $e = new \Exception();
        $trace = $e->getTrace();
        //position 0 would be the line that called this function so we ignore it
        $lastCall = $trace[1];

        if(!isset($lastCall['function']) || !isset($lastCall['class'])){
            throw new ServiceException("getAnnotation helper has been called from wrong environment");
        }
        return new AnnotationsHelper($this, $lastCall['function']);

    }

}
