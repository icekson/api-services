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
class Description {
    /**
     * @var string
     */
    public $value;

    public function __construct(array $values){
        if(!isset($values['value'])){
            $values['value'] = "";
        }

        $this->value = $values['value'];
    }
}