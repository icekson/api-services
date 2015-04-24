<?php

namespace Api;


use Api\Service\Exception\BadTokenException;
use Api\Service\Exception\NoTokenException;
use Api\Service\Exception\NotPermittedException;
use Api\Service\Permission\Checker;
use Api\Service\Permission\RolesParser;
use Api\Service\PropertiesAwareInterface;
use Api\Service\RemoteServiceInterface;
use Api\Service\AccessLoggerInterface;
use \Api\Service\Response\Builder as ResponseBuilder;

use Api\Service\Exception\ServiceException;
use Api\Service\Exception\ServiceNotFoundException;
use Api\Service\SecurityOwnerPermissionInterface;
use Api\Service\SecurityServiceInterface;
use Api\Service\ServiceFinder;
use \Api\Service\Util\Properties;
use \Api\Service\Response\ResponseBuilderAwareInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Rbac\Role\RoleInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;


class Dispatcher implements ResponseBuilderAwareInterface, PropertiesAwareInterface, ServiceLocatorAwareInterface
{

    const ACCESS_TOKEN_PARAM_NAME = "access_token";

    private $debug = true;


    /**
     *
     * @var ResponseBuilder
     */
    private $builder = null;

    /**
     * @var Properties
     */
    private $params;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceManager;


    /**
     * @var \ArrayObject
     */
    private $servicePaths = null;


    /**
     * @var RemoteServiceInterface|null
     */
    private $calledService = null;

    /**
     * @var RoleInterface[]|null
     */
    private $roles = null;
	
	
	/**
	* @var AccessLoggerInterface
	**/
	private $accessLogger = null;
	
    /**
     * @var \Doctrine\Common\Annotations\AnnotationReader
     */
    private $annotationsReader = null;

    public function __construct()
    {
        AnnotationRegistry::registerAutoloadNamespace('Api\\', __DIR__ . "/../");
        $this->annotationsReader = new AnnotationReader();
        $this->servicePaths = new \ArrayObject();
    }
	
	/**
	*
	* @param AccessLoggerInterface $logger
	* @return $this;
	**/
	public function setAccessLogger(AccessLoggerInterface $logger){
		$this->accessLogger = $logger;		
		return $this;
	}

    public function dispatch($serviceName, $action, array $params, ResponseBuilder &$builder = null, ServiceLocatorInterface $sm = null)
    {
        $this->calledService = null;
        if ($builder !== null) {
            $this->setResponseBuilder($builder);
        }

        if ($sm !== null) {
            $this->setServiceLocator($sm);
        }
        $this->setProperties(new Properties($params));
        if ($this->servicePaths->count() === 0) {
            throw new ServiceException("No services is registered");
        }

        try {
            $token = $this->retrieveToken();

            $serviceFound = false;
            $finder = new ServiceFinder();
            foreach ($this->servicePaths as $path) {
                $dir = new \DirectoryIterator($path);

                if ($dir === false) {
                    continue;
                }

                $classes = $finder->scanFolder($dir);
                if (count($classes) === 0) {
                    continue;
                }

                foreach ($classes as $reflClass) {
                    $classAnn = $this->annotationsReader->getClassAnnotation($reflClass, 'Api\Service\Annotation\Service');
                    $methodAnn = null;
                    if ($classAnn !== null && $classAnn instanceof \Api\Service\Annotation\Service && $classAnn->name === $serviceName) {
                        $methods = $reflClass->getMethods();
                        $foundMethod = null;
                        foreach ($methods as $m) {
                            $methodAnn = $this->annotationsReader->getMethodAnnotation($m, 'Api\Service\Annotation\ServiceAction');
                            if ($methodAnn !== null && $methodAnn instanceof \Api\Service\Annotation\ServiceAction && $methodAnn->name === $action) {
                                $foundMethod = $m;
                                $serviceFound = true;
                                break;
                            }
                        }
                        if ($foundMethod !== null) {
							$currentHttpMethod = isset($_SERVER['REQUEST_METHOD']) && !empty($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "GET";
							if(!empty($methodAnn->method) && strtoupper($methodAnn->method) !== $currentHttpMethod){
								throw new NotPermittedException("Incorrect HTTP request method");
							}
                            $this->calledService = $service = $this->createServiceInstance($reflClass);
                            if ($this->checkPermissions($service, $classAnn->name, $methodAnn->name, $token)) {
                                $foundMethod->invoke($service);
								if($this->accessLogger instanceof AccessLoggerInterface){
									$this->accessLogger->log($token, $service->getProperties(), $service->getResponseBuilder());									
								}
                            } else {
                                throw new NotPermittedException("You have no permission to this API");
                            }
                        }
                    }
                }

            }
            if (!$serviceFound) {
                throw new ServiceNotFoundException("Service action with name '{$action}' not found on this api");
            }
        } catch (BadTokenException $ex) {
            $this->getResponseBuilder()->setStatusCode(ResponseBuilder::STATUS_CODE_BAD_TOKEN);
            $this->getResponseBuilder()->setError($ex->getMessage());
        } catch (ServiceNotFoundException $ex) {
            $this->getResponseBuilder()->setStatusCode(ResponseBuilder::STATUS_CODE_NOT_FOUND);
            $this->getResponseBuilder()->setError($ex->getMessage());
        } catch (NoTokenException $ex) {
            $this->getResponseBuilder()->setStatusCode(ResponseBuilder::STATUS_CODE_EMPTY_TOKEN);
            $this->getResponseBuilder()->setError($ex->getMessage());
        } catch (NotPermittedException $ex) {
            $this->getResponseBuilder()->setStatusCode(ResponseBuilder::STATUS_CODE_NOT_PERMITTED);
            $this->getResponseBuilder()->setError($ex->getMessage());
        } catch (\Exception $ex) {
            if ($this->debug) {
                throw $ex;
            } else {
                $sm->get('Api\App')->log->error($ex->getMessage());
                $this->getResponseBuilder()->setError("api service error");
            }
            //$this->builder->setStatus(ResponseBuilder::STATUS_ERROR)->setMessages("service action '$name / $action' does not exists, or caused some problems");
        }
        $data = $this->getResponseBuilder()->getData();
       /* if($this->getResponseBuilder()->getStatusCode() == ResponseBuilder::STATUS_CODE_SUCCESS && empty($data)){
            $this->getResponseBuilder()->setStatusCode(ResponseBuilder::STATUS_CODE_EMPTY_RESULT);
        }*/
        return $this->getResponseBuilder()->result();
    }

    /**
     * @return RemoteServiceInterface|null
     */
    public function getCalledService()
    {
        return $this->calledService;
    }

    /**
     * @param string $servicesPath
     * @return $this
     */
    public function registerServicesPath($servicesPath)
    {
        $this->servicePaths->append($servicesPath);
        return $this;
    }

    /**
     * @return ResponseBuilder
     */
    public function &getResponseBuilder()
    {
        return $this->builder;
    }


    /**
     * @param RemoteServiceInterface $service
     * @param $serviceName
     * @param $methodName
     * @param $token
     * @return mixed
     * @throws BadTokenException
     */
    private function checkPermissions(RemoteServiceInterface $service, $serviceName, $methodName, $token)
    {
        if ($service instanceof SecurityServiceInterface) {
            $res = $service->isPermitted($token);
            if (!$res) {
                throw new BadTokenException("Bad token is given");
            }
            $roles = $this->getRole($service, $token);
            $checker = new Checker();
            $rolePermission = $checker->checkPermission($serviceName, $methodName, $roles);
            if (!$rolePermission) {
                return $rolePermission;
            }
        }

        if ($service instanceof SecurityOwnerPermissionInterface) {
            return $service->checkOwnPermission();
        }
        return true;

    }


    /**
     * @param \ReflectionClass $class
     * @return RemoteServiceInterface
     */
    private function createServiceInstance(\ReflectionClass $class)
    {
        $service = $class->newInstance();
        if (!$service instanceof RemoteServiceInterface) {
            throw new \InvalidArgumentException("Service class should implements RemoteServiceInterface");
        }
        if ($service instanceof ResponseBuilderAwareInterface) {
            $service->setResponseBuilder($this->getResponseBuilder());
        }
        if ($service instanceof ServiceLocatorAwareInterface) {
            $service->setServiceLocator($this->getServiceLocator());
        }
        if ($service instanceof PropertiesAwareInterface) {
            $service->setProperties($this->getProperties());
        }
        return $service;
    }

    /**
     * @return string
     * @throws NoTokenException
     */
    public function retrieveToken()
    {
        $token = $this->getProperties()->get(self::ACCESS_TOKEN_PARAM_NAME, null);
        if ($token === null) {
            throw new NoTokenException("Access token is empty");
        }
        return $token;
    }

    /**
     * @param SecurityServiceInterface $service
     * @param $token
     * @return RoleInterface[]
     * @throws BadTokenException
     */
    public function getRole(SecurityServiceInterface $service, $token)
    {
        if ($this->roles === null) {
            $identity = $service->getIdentity();
            if ($identity === null) {
                throw new BadTokenException("Bad token is given");
            }
            $roles = $identity->getRoles();
            $configPath = APP . "/config/permissions.php";
            if (!file_exists($configPath)) {
                throw new \InvalidArgumentException("Config file is not found, given : '{$configPath}'");
            }
            $parser = new RolesParser(include_once $configPath);
            $rolesList = $parser->parse();
            foreach ($rolesList as $r) {
                foreach ($roles as $cr) {
                    if ($r->getName() === $cr) {
                        $resRoles[] = $r;
                    }
                }
            }
            $this->roles = $resRoles;
        }
        return $this->roles;
    }

    /**
     * @param \Api\Service\Response\Builder $builder
     * @return mixed
     */
    public function setResponseBuilder(ResponseBuilder $builder)
    {
        $this->builder = $builder;
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
}
