<?php

/**
 * @author a.itsekson
 */


namespace Api\Service\Response;
use Api\Service\Response\Builder as ResponseBuilder;

class ObjectBuilder implements ResponseBuilder {

    protected $data = "";

    protected $status = self::STATUS_SUCCESS;

    protected $messages = array();

    protected $rootElementName = "data";

    protected $customElements = array();

    protected $statusCode = self::STATUS_CODE_SUCCESS;
    
    protected $errorCode = 0;


    protected $customResponse = null;

    public function setCustomResponse($resp){
        $this->customResponse = $resp;
        return $this;
    }

    public function setStatus($status) {
        $this->status = (string) $status;
        return $this;
    }

    public function setMessages($messages) {
        if (is_string($messages)) {
            $this->messages [] = $messages;
        } else {
            $this->messages = $messages;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getMessagesAsString() {
        return $this->messageToString($this->messages);
    }

    public function setError($msg, $level = self::ERROR_LEVEL_WARNING){
        $this->setStatus(ResponseBuilder::STATUS_ERROR);
        if($this->getStatusCode() === self::STATUS_CODE_SUCCESS){
            if($level === self::ERROR_LEVEL_CRITICAL){
                $this->setStatusCode(self::STATUS_CODE_ERROR);
            }else {
                $this->setStatusCode(self::STATUS_CODE_WARNING);
            }
        }
        $this->setMessages($msg);
        return $this;
    }
    
    public function setErrorCode($code){
        $this->errorCode = (int)$code;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    public function getData() {
        return $this->data;
    }

    public function isError(){
        return $this->status == ResponseBuilder::STATUS_ERROR;
    }


    public function setRootElementName($name) {
        $this->rootElementName = $name;
        return $this;
    }

    public function addCustomElement($name, $value) {
        $this->customElements [$name] = $value;
        return $this;
    }



    public function result() {
        $result = $this->_getResult();
        return $result;
    }

    protected function _getResult(){
        if($this->customResponse === null){
            $result =  array(
                "status" => $this->status,                
                "success" => $this->status == self::STATUS_SUCCESS,
                "statusCode": $this->errorCode,
                "message" => $this->messageToString($this->messages),
                "{$this->rootElementName}" => $this->data
            );            
            foreach ( $this->customElements as $key => $val ) {
                $result [$key] = $val;
            }
        }else{
            $result = $this->customResponse;
        }
        return $result;
    }

    private function messageToString($messages){
        $res = "";
        foreach($messages as $m){
            $res .= $m . ";\n";
        }
        $res = trim($res, ";\n");
        return $res;
    }

    /**
     * @param $code
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

}