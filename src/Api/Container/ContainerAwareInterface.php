<?php
/**
 * @author a.itsekson
 * @createdAt: 11.04.2017 18:58
 */

namespace Api\Container;


use Psr\Container\ContainerInterface;

interface ContainerAwareInterface
{
    /**
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function setContainer(ContainerInterface $container);

}