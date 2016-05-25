<?php
/** 
 * @author: a.itsekson
 * @date: 07.02.2015 
 */

namespace Api\Service;


use Zend\Cache\Storage\Adapter\Memory;
use Zend\Cache\Storage\StorageInterface;

class ServiceFinder {

    /** @var null|StorageInterface */
    private static $cache = null;

    /**
     * ServiceFinder constructor.
     * @param StorageInterface|null $storage
     */
    public function __construct(StorageInterface $storage = null)
    {
        if(self::$cache === null){
            if($storage !== null){
                self::$cache = $storage;
            }else {
                self::$cache = new Memory();
            }
        }
    }

    /**
     * @param StorageInterface $storage
     */
    public function setCacheStorage(StorageInterface $storage)
    {
        self::$cache = $storage;
    }

    /**
     * @return \ReflectionClass[]
     */
    public function scanFolder(\DirectoryIterator $dir){
        $res = array();

        $key = $this->createCacheKey($dir);
        $fromCache = null;
        if(self::$cache->hasItem($key)){
            $fromCache = @unserialize(self::$cache->getItem($key));
            $res = [];
            if($fromCache) {
                foreach ($fromCache as $c) {
                    $res[] = new \ReflectionClass($c->name);
                }
                $fromCache = $res;
            }
        }
        if($fromCache){
            return $fromCache;
        }

        foreach ( $dir as $file ) {
            if($file->isFile()){
                $classes = $this->extractClassesNames($file->getFileInfo());
                $className = isset($classes[0]) ? $classes[0] : null;
                if($className === null){
                    continue;
                }
                $reflClass = new \ReflectionClass($className);
                if($reflClass->implementsInterface("Api\\Service\\RemoteServiceInterface")){
                    $res[] = $reflClass;
                }
            }
        }
        self::$cache->setItem($key, @serialize($res));
        return $res;
    }

    private function createCacheKey(\DirectoryIterator $dir)
    {
        $key = md5($dir->getPathname());
        return $key;
    }


    private function extractClassesNames(\SplFileInfo $file){
        $php_code = file_get_contents ( $file->getPathname() );
        $classes = array ();
        $namespace="";
        $tokens = token_get_all ( $php_code );
        $count = count ( $tokens );

        for($i = 0; $i < $count; $i ++)
        {
            if ($tokens[$i][0]===T_NAMESPACE)
            {
                for ($j=$i+1;$j<$count;++$j)
                {
                    if ($tokens[$j][0]===T_STRING)
                        $namespace.="\\".$tokens[$j][1];
                    elseif ($tokens[$j]==='{' or $tokens[$j]===';')
                        break;
                }
            }
            if ($tokens[$i][0]===T_CLASS)
            {
                for ($j=$i+1;$j<$count;++$j)
                    if ($tokens[$j]==='{')
                    {
                        $classes[]=$namespace."\\".$tokens[$i+2][1];
                    }
            }
        }
        return $classes;
    }
}