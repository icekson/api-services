<?php

/**
 * @author a.itsekson;
 */

namespace Api\Service;


use Api\Service\Response\ResponseBuilderAwareInterface;

interface RemoteServiceInterface extends ResponseBuilderAwareInterface, PropertiesAwareInterface{

    /**
     * @return AnnotationsHelper
     */
    public function getAnnotationsHelper();

}