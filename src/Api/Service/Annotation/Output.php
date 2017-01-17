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
class Output {
    /**
     * @var string
     */
    public $name;

    public $type = "string";


    public function __construct(array $values){
        if(!isset($values['name'])){
            $values['name'] = null;
        }

        if(!isset($values['type'])){
            $values['type'] = $this->type;
        }

        $this->name = $values['name'];
        $this->type = $values['type'];
    }
}