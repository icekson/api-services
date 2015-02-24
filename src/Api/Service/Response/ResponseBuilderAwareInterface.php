<?php
/**
 * @author a.itsekson
 */


namespace Api\Service\Response;

interface ResponseBuilderAwareInterface{

	/**
 * @param \Api\Service\Response\Builder $builder
 * @return mixed
 */
	public function setResponseBuilder(\Api\Service\Response\Builder $builder);

	/**
	 * @return \Api\Service\Response\Builder
	 */
	public function getResponseBuilder();
}