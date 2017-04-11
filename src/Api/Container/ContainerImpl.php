<?php
/**
 * @author a.itsekson
 * @createdAt: 11.04.2017 18:59
 */

namespace Api\Container;


use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContainerImpl implements ContainerInterface
{
    /**
     * @var \ArrayObject|null
     */
    private $factories = null;

    private $lazy = true;

    /**
     * ContainerImpl constructor.
     * @param array $config
     * @param bool $lazy
     */
    public function __construct($config = [], $lazy = true)
    {
        $this->factories = new \ArrayObject();
        foreach ($config as $key => $item) {
            if(!is_callable($item)){
                $item = function() use ($item){
                    return $item;
                };
            }
            $this->set($key, $item);
        }
        $this->lazy = $lazy;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ContainerException
     */
    public function get($id)
    {
        if(!isset($this->factories[$id])){
            throw new ContainerException("Factory for '{$id}' key is not set");
        }
        $factory = $this->factories[$id];
        if($factory["instance"] === null){
            $factory["instance"] = $factory["factory"]($this);
        }
        return $factory["instance"];
    }

    /**
     * @param string $id
     * @return bool
     */
    public function has($id)
    {
        return $this->factories->offsetExists($id);
    }

    /**
     * @param $id
     * @param callable $factory
     * @return $this
     */
    public function set($id, Callable $factory)
    {
        $this->factories[$id] = [
            "id" => $id,
            "factory" => $factory,
            "instance" => null
        ];
        return $this;
    }

}