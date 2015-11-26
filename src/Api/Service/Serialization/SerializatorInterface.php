<?php
/**
* @author a.itsekson
* @date 2015-11-26
**/

namespace Api\Service\Serialization;

interface SerializatorInterface{
	
	public function serialize(IArrayExchange $entity, array $neededColumns);
}