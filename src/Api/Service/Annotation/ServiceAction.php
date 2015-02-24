<?php

/**
 * @author a.itsekson
 */

namespace Api\Service\Annotation;

use Doctrine\Common\Annotations\Annotation,
    Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class ServiceAction
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
class ServiceAction {
    public $name;
    public $method;
}