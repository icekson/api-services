<?php

/**
 * @author a.itsekson
 */


namespace Api\Service\Response;

interface Builder {

    const STATUS_SUCCESS = "success";

    const STATUS_ERROR = "error";

    const STATUS_CODE_ERROR = 500;
    const STATUS_CODE_WARNING = 400;
    const STATUS_CODE_BAD_TOKEN = 403;
    const STATUS_CODE_NOT_PERMITTED = 403;
    const STATUS_CODE_EMPTY_TOKEN = 401;
    const STATUS_CODE_EMPTY_RESULT = 204;
    const STATUS_CODE_SUCCESS = 200;
    const STATUS_CODE_NOT_FOUND = 404;

    const ERROR_LEVEL_VALIDATION = 0;
    const ERROR_LEVEL_WARNING = 1;
    const ERROR_LEVEL_CRITICAL = 2;

    public function setStatusCode($code);
    public function getStatusCode();

    /**
     * 
     * @param string $status
     * @return Builder
     */
    public function setStatus($status);

    public function setError($msg, $level = self::ERROR_LEVEL_WARNING);
    
    public function setErrorCode($code);

    public function setCustomResponse($resp);

    /**
     * @return string
     */
    public function getMessagesAsString();
    /**
     * 
     * @param string|array $messages
     * @return Builder
     */
    public function setMessages($messages);

    /**
     * 
     * @param string|array|\stdClass $data
     * @return Builder
     */
    public function setData($data);

    /**
     * @return bool
     */
    public function isError();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * 
     * @param string $name
     * @return Builder
     */
    public function setRootElementName($name);

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @return Builder
     */
    public function addCustomElement($name, $value);

    public function result();
}