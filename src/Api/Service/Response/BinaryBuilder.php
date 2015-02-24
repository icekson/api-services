<?php

/**
 * @author a.itsekson
 */


namespace Api\Service\Response;

use Api\Service\Response\ObjectBuilder;



class BinaryBuilder extends ObjectBuilder {

    public function result(){
        return $this->data;
    }

    public function setData($data){
        if(!is_string($data)){
            throw new \InvalidArgumentException("Invalid type of given data parameter");
        }
        $this->data = $data;
        return $this;
    }

}