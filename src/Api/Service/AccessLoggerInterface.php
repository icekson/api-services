<?php

/**
 * @author a.itsekson
 */

namespace Api\Service;


use Api\Service\Util\Properties;
use Api\Service\Response\Builder as ResponseBuilder;

interface AccessLoggerInterface {
	
	/**
	* @param string $accessToken
	* @param Properties $params
	* @param ResponseBuilder $response	
	**/
    public function log($accessToken, Properties $params, ResponseBuilder $response);
} 