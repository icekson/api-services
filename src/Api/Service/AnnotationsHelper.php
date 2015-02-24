<?php
/**
 * @author a.itsekson
 * @date 09.02.2015
 * 
 */

namespace Api\Service;


use Api\Service\Annotation\Acceptable;
use Api\Service\Annotation\AcceptableColumns;
use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationsHelper {

    /**
     * @var \ReflectionMethod
     */
    private $callingMethod = null;

    /**
     * @var \ReflectionClass
     */
    private $callingService = null;

    /**
     * @var RemoteServiceInterface|null
     */
    private $callingServiceInstance = null;

    /**
     * @var AnnotationReader
     */
    private $reader = null;

    public function __construct(RemoteServiceInterface $service, $methodName){
        $this->callingService = new \ReflectionClass($service);
        $this->callingServiceInstance = $service;
        $refl = new \ReflectionClass($service);
        $this->callingMethod = $refl->getMethod($methodName);
        if($this->callingMethod === null){
            throw new \InvalidArgumentException("Invalid method name is given. Class '{$refl->getName()}' does not contain method '$methodName'");
        }
        $this->reader = new AnnotationReader();
    }

    /**
     * @return array
     */
    public function getAcceptableFilters(){
        $anns = $this->reader->getMethodAnnotations($this->callingMethod);
        $identity = null;
        if($this->callingServiceInstance instanceof \Api\Service\SecurityServiceInterface){
            $identity = $this->callingServiceInstance->getIdentity();
        }
        $res = array();
        if(is_array($anns)){
            foreach($anns as $ann){
                if($ann instanceof \Api\Service\Annotation\AcceptableFilters){
                    $res[] = $ann;
                }
            }
        }

        $annotation = $this->determineByIdentity($res, $identity);
        if($annotation instanceof \Api\Service\Annotation\AcceptableFilters){
            return $annotation->value;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getAcceptableColumns(){
        $anns = $this->reader->getMethodAnnotations($this->callingMethod);
        $identity = null;
        if($this->callingServiceInstance instanceof \Api\Service\SecurityServiceInterface){
            $identity = $this->callingServiceInstance->getIdentity();
        }
        $res = array();
        if(is_array($anns)){
            foreach($anns as $ann){
                if($ann instanceof \Api\Service\Annotation\AcceptableColumns){
                    $res[] = $ann;
                }
            }
        }

        $annotation = $this->determineByIdentity($res, $identity);
        if($annotation instanceof \Api\Service\Annotation\AcceptableColumns){
            return $annotation->value;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getAcceptableGroupings(){
        $anns = $this->reader->getMethodAnnotations($this->callingMethod);
        $identity = null;
        if($this->callingServiceInstance instanceof \Api\Service\SecurityServiceInterface){
            $identity = $this->callingServiceInstance->getIdentity();
        }
        $res = array();
        if(is_array($anns)){
            foreach($anns as $ann){
                if($ann instanceof \Api\Service\Annotation\AcceptableGroupings){
                    $res[] = $ann;
                }
            }
        }

        $annotation = $this->determineByIdentity($res, $identity);
        if($annotation instanceof \Api\Service\Annotation\AcceptableGroupings){
            return $annotation->value;
        }
        return [];
    }

    /**
     * @param Acceptable $annotations
     * @param IdentityInterface $identity
     * @return null|AcceptableColumns
     */
    private function determineByIdentity($annotations, IdentityInterface $identity = null){
        $res = null;
        $default = null;
        foreach($annotations as $ann){
            if($ann->role === 'default'){
                $default = $ann;
                break;
            }
        }
        if($identity !== null){
            foreach($annotations as $ann){
                $roles = $identity->getRoles();
                if($ann->role !== 'default' && in_array($ann->role, $roles)){
                    $res = $ann;
                    break;
                }
            }
        }

        if($res !== null){
            if($res->extendDefault && $default !== null){
                $values = $default->value;
                $r = array_merge($values, $res->value);
                $res->value = $r;
            }
        }
        return $res !== null ? $res : $default;

    }

    /**
     * @return string
     */
    public function getServiceActionName(){
        $annotation = $this->reader->getMethodAnnotation($this->callingMethod, "Api\\Service\\Annotation\\ServiceAction");
        if($annotation instanceof \Api\Service\Annotation\ServiceAction){
            return $annotation->name;
        }
        return null;
    }

    /**
     * @return string
     */
    public function getServiceName(){
        $annotation = $this->reader->getClassAnnotation(new \ReflectionClass($this->callingServiceInstance), "Api\\Service\\Annotation\\Service");
        if($annotation instanceof \Api\Service\Annotation\Service){
            return $annotation->name;
        }
        return null;
    }
}