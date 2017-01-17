<?php
/**
 * @author a.itsekson
 * @createdAt: 17.01.2017 13:03
 */

namespace Api\Service\Util;


use Api\Dispatcher;
use Api\Service\Response\XmlBuilder;
use Api\Service\ServiceFinder;
use Doctrine\Common\Annotations\AnnotationReader;
use Zend\Cache\Storage\Adapter\Memory;
use Zend\Cache\Storage\StorageInterface;

class ApiDocumentator
{

    /**
     * @var null|ServiceFinder
     */
    private $finder = null;

    /**
     * @var array
     */
    private $paths = [];

    /**
     * @var null|AnnotationReader
     */
    private $reader = null;

    /**
     * @var null|StorageInterface
     */
    private $cache = null;

    private $type = "json";


    public function __construct(Dispatcher $dispatcher)
    {
        $this->paths = $dispatcher->getServicesPaths();
        $this->reader = $dispatcher->getAnnotationsReader();
        $this->finder = new ServiceFinder();

        $resp = $dispatcher->getResponseBuilder();
        if($resp instanceof XmlBuilder){
            $this->type = "xml";
        }
    }

    /**
     * @param StorageInterface|null $cache
     * @return array|mixed
     */
    public function generateDocs(StorageInterface $cache = null)
    {
        if($this->cache === null){
            if($cache !== null){
                $this->cache = $cache;
            }else {
                $this->cache = new Memory();
            }
        }

        $key = "_api_services_.api.docs";
        $res = false;
        $data = $this->cache->getItem($key, $res);
        if($res && !empty($data)){
            return $data;
        }
        $res = [];
        foreach ($this->paths as $path) {
            $dir = new \DirectoryIterator($path);
            if ($dir === false) {
                continue;
            }
            $classes = $this->finder->scanFolder($dir);
            if (count($classes) === 0) {
                continue;
            }

            foreach ($classes as $reflClass) {
                $classAnn = $this->reader->getClassAnnotation($reflClass, 'Api\Service\Annotation\Service');
                $methodAnn = null;
                if ($classAnn !== null && $classAnn instanceof \Api\Service\Annotation\Service) {
                    $methods = $reflClass->getMethods();
                    $foundMethod = null;
                    foreach ($methods as $m) {
                        $methodAnn = $this->reader->getMethodAnnotation($m, 'Api\Service\Annotation\ServiceAction');
                        if ($methodAnn !== null && $methodAnn instanceof \Api\Service\Annotation\ServiceAction) {
                            // method is service action

                            $apiDesc = [
                                "name" => $methodAnn->name,
                                "url" => $classAnn->name . "/" . $methodAnn->name,
                                "description" => "",
                                "method" => strtoupper($methodAnn->method),
                                "type" => $this->type,
                                "inputParams" =>[]
                            ];
                            $annotations = $this->reader->getMethodAnnotations($m);
                            if(!empty($annotations)){
                                foreach ($annotations as $annotation) {
                                    if($annotation instanceof \Api\Service\Annotation\Description){
                                        $apiDesc['description'] = $annotation->value;
                                    }else if($annotation instanceof \Api\Service\Annotation\Input){
                                        $apiDesc['inputParams'][] = [
                                            "name" => $annotation->name,
                                            "type" => $annotation->type,
                                            "required" => $annotation->required,
                                            "acceptableValues" => $annotation->acceptableValues
                                        ];
                                    }
                                }
                            }

                            if(!isset($res[$classAnn->name]) || !is_array($res[$classAnn->name])){
                                $res[$classAnn->name] = [];
                            }
                            $res[$classAnn->name][] = $apiDesc;

                        }
                    }
                }
                usort($res[$classAnn->name], function($a, $b){
                    if($a["name"] > $b['name']){
                        return 1;
                    }else if($a["name"] < $b['name']){
                        return -1;
                    } else{
                        return 0;
                    }
                });
            }

        }

        $this->cache->setItem($key, $res);
        return $res;

    }

    /**
     * @param StorageInterface $cache
     * @return $this
     */
    public function setCacheImpl(StorageInterface $cache)
    {
        $this->cache = $cache;
        return $this;
    }
}