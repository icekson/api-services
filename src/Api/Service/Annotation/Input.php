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
class Input {
    /**
     * @var string
     */
    public $name;

    public $type = "string";

    public $required = false;

    public $description = "";

    public $acceptableValues = [];


    public function __construct(array $values){
        if(!isset($values['name'])){
            $values['name'] = null;
        }

        if(!isset($values['type'])){
            $values['type'] = $this->type;
        }

        if(!isset($values['required'])){
            $values['required'] = $this->required;
        }

        if(!isset($values['description'])){
            $values['description'] = $this->description;
        }

        $this->name = $values['name'];
        $this->type = $values['type'];
        $this->description = $values['description'];
        $this->required = $values['required'] === "true" ? true : false;


        if($this->type === "array"){
            if(isset($values['acceptableValues']) && !empty($values['acceptableValues'])){
                if(!is_array($values['acceptableValues'])) {
                    $tmp = explode(",", $values['acceptableValues']);
                }else{
                    $tmp = $values['acceptableValues'];
                }
                if(!$tmp){
                    $this->acceptableValues = [];
                }else{
                    $this->acceptableValues = $tmp;
                }
            }
        }

    }
}