<?php

/**
 * @author a.itsekson
 */

namespace Api\Service\Annotation;

use Doctrine\Common\Annotations\Annotation,
    Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Service
 * @Annotation
 * @Target({"METHOD"})
 *
 */
class AcceptableGroupings extends Acceptable {

}