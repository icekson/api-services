<?php
/**
 * @author a.itsekson
 */

namespace Api\Service\Response;

use Api\Service\Response\Builder as ResponseBuilder;

class XmlBuilder extends ObjectBuilder {
	private $xml = null;

	public function result() {
		$xml = new \XMLWriter ();
		$this->xml = $xml;
		$xml->openMemory ();
		$xml->startElement ( "response" );
		$xml->writeElement ( "status", $this->status );
		$xml->startElement ( "messages" );
		foreach ( $this->messages as $message ) {
			$xml->writeElement ( "message", $message );
		}
		$xml->endElement ();
		$data = ( array ) $this->data;
		if (count ( $data ) > 0) {
			$xml->startElement ( $this->rootElementName );
			array_walk_recursive ( $data, array (
					$this,
					'addElement' 
			) );
		} else {
			$xml->writeElement ( $this->rootElementName );
		}
		$xml->endElement ();
		$xml->endElement ();
		$res = $xml->outputMemory ( true );
		
		return $res;
	}
	private function addElement($val, $key) {
		if (is_int ( $key ) && !is_string($val)) {
			$this->xml->startElement ( "item" );
			foreach ( ( array ) $val as $k => $v ) {
				$k = preg_replace ( "/\W+/i", "", $k );
				if (is_string ( $k ) && strlen ( $k ) > 0) {
					$this->xml->writeElement ( $k, $v );
				}
			}
			$this->xml->endElement ();
		} else {
			$key = preg_replace ( "/\W+/i", "", $key );
			if (is_string ( $key ) && strlen ( $key ) > 0) {
				$this->xml->writeElement ( $key, $val );
			}
		}
	}

}