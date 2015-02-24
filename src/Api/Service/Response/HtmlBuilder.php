<?php

namespace Api\Service\Response;

class HtmlBuilder implements Response\Builder {

	private $statusCode = 200;

	private $xml = null;
	private $data = array ();
	private $status = self::STATUS_SUCCESS;
	private $messages = array ();
	public function setStatus($status) {
		$this->status = ( string ) $status;
		return $this;
	}
	public function setMessages($messages) {
		if (is_string ( $messages )) {
			$this->messages [] = $messages;
		} else {
			$this->messages = $messages;
		}
		return $this;
	}
	public function setData($data) {
		$this->data = $data;
		return $this;
	}
    public function setError($msg){
        $this->setStatus(Response\Builder::STATUS_ERROR);
        $this->setMessages($msg);
        return $this;
    }
    public function getData(){
        return $this->data;
    }

    public function isError(){
        return $this->status == Response\Builder::STATUS_ERROR;
    }

	public function result() {
		return $this->data;
	}
	private function addElement($val, $key) {

	}

    /**
     *
     * @param string $name
     * @return Response\Builder
     */
    public function setRootElementName($name)
    {

    }

    /**
     *
     * @param string $name
     * @param mixed $value
     * @return Response\Builder
     */
    public function addCustomElement($name, $value)
    {

    }

	public function setStatusCode($code)
	{
		$this->statusCode = $code;
	}

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	public function setCustomResponse($resp)
	{

	}
}