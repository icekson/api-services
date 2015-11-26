<?php

namespace Api;

use Api\Service\AnnotatedServiceInterface;
use Api\Service\AnnotationsHelper;
use Api\Service\ConfigurableServiceInterface;
use Api\Service\Exception\ServiceException;
use Api\Service\RemoteServiceInterface;
use Api\Service\Response\Builder as ResponseBuilder;
use Api\Service\Serialization\DefaultSerializator;
use Api\Service\Serialization\SerializatorInterface;
use Api\Service\Util\IArrayExchange;
use Api\Service\Util\Properties;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


abstract class BaseService implements ServiceLocatorAwareInterface, RemoteServiceInterface, AnnotatedServiceInterface{

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

    /**
     * @param AnnotationsHelper $helper
     * @return array
     */
    public function getColumns(AnnotationsHelper $helper) {
        $acceptableColumns = $helper->getAcceptableColumns();
        $colsString = $this->getProperties()->get('columns', 'all');
        if (is_string($colsString) && trim($colsString) === 'all') {
            return $acceptableColumns;
        }
        if (!is_array($colsString)) {
            $cols = explode(",", $colsString);
        } else {
            $cols = $colsString;
        }
        $res = [];
        foreach ($cols as $col) {
            if (!in_array(trim($col), $acceptableColumns)) {
                throw new \InvalidArgumentException("You are trying to request is not an acceptable column name '$col'. Here is the list of acceptable columns: " . implode(", ", $acceptableColumns));
            } else {
                $res[] = $col;
            }
        }
        return array_unique(array_values($res));
    }

    /**
     * @param AnnotationsHelper $helper
     * @return array
     */
    public function getFilters(AnnotationsHelper $helper) {
        $acceptableFilters = $helper->getAcceptableFilters();

        $filters = $this->getProperties()->get('filters', []);
        $res = [];
        foreach ($filters as $name => $filter) {
            if (!in_array(trim($name), $acceptableFilters)) {
                throw new \InvalidArgumentException("You are trying to use is not an acceptable filter name '$name'. Here is the list of acceptable filters: " . implode(", ", $acceptableFilters));
            } else {
                $res[$name] = $filter;
            }
        }
        return $res;
    }

    /**
     * @param AnnotationsHelper $helper
     * @return array
     */
    public function getParameters() {
        $parameters = $this->getProperties()->get('parameters', []);

        return $parameters;
    }

    /**
     * @param AnnotationsHelper $helper
     * @return array
     */
    public function getGroupings(AnnotationsHelper $helper) {
        $acceptableGroupings = $helper->getAcceptableGroupings();
        $colsString = $this->getProperties()->get('group', null);
        if ($colsString === null) {
            return [];
        }
        if (!is_array($colsString)) {
            $cols = explode(",", $colsString);
        } else {
            $cols = $colsString;
        }
        $res = [];
        foreach ($cols as $col) {
            if (!in_array(trim($col), $acceptableGroupings)) {
                throw new \InvalidArgumentException("You are trying to use is not an acceptable grouping column name '$col'. Here is the list of acceptable grouping columns: " . implode(", ", $acceptableGroupings));
            } else {
                $res[] = $col;
            }
        }
        return array_unique(array_values($res));
    }

    /**
     * @param IArrayExchange $array
     * @return SerializatorInterface
     */
    public function createSerializator(IArrayExchange $array)
    {
        $serializator = new DefaultSerializator($array, $this);
        return $serializator;
    }

}
