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
class Acceptable {
    /**
     * @var array
     */
    public $value;

    public $role = "default";

    public $extendDefault = false;


    public function __construct(array $values){
        if(!isset($values['value'])){
            $values['value'] = [];
        }

        if(is_string($values['value'])){
            $values['value'] = array($values['value']);
        }

        if(!is_array($values['value'])){
            throw new \InvalidArgumentException("@AcceptableColumns or @AcceptableFilters annotation accept only array of values");
        }
        $this->value = $values['value'];


        if(!isset($values['role'])){
            $values['role'] = "default";
        }
        $this->role = $values['role'];

        if(!isset($values['extendDefault'])){
            $values['extendDefault'] = false;
        }
        $this->extendDefault = $values['extendDefault'];

    }
}