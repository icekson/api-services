<?php
/**
 * @author a.itsekson
 */


namespace Api\Service;

use Api\Service\Util\Properties;

interface PropertiesAwareInterface{
	
	/**
	 * @param Properties $props
	 * @return mixed
	 */
	public function setProperties(Properties $props);

	/**
	 * @return Properties
	 */
	public function getProperties();
}