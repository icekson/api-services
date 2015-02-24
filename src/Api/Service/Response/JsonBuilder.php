<?php

/**
 * @author a.itsekson
 */


namespace Api\Service\Response;

use Api\Service\Response\ObjectBuilder;
use JsonSerializable;



class JsonBuilder extends ObjectBuilder implements JsonSerializable {

    public function result(){
        $result = parent::result();
        return json_encode($result);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->_getResult();
    }
}