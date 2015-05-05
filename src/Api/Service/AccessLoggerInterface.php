<?php

/**
 * @author a.itsekson
 */

namespace Api\Service;


use Api\Service\Util\Properties;
use Api\Service\Response\Builder as ResponseBuilder;

interface AccessLoggerInterface {

    /**
     * @param $accessToken
     * @param UserIdentity $identity
     * @param array $params
     * @param ResponseBuilder $response
     * @return mixed
     */
    public function log($accessToken, $identity, $params, ResponseBuilder $response);
} 