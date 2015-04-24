<?php
/**
 * @author a.itsekson
 */

namespace Api\Service\Response;

use Api\Service\Response\Builder as ResponseBuilder;

class XmlBuilder extends ObjectBuilder {

	private $xml = null;

	public function result() {
        $msgs = array();

        foreach($this->messages as $msg){
            $m = array();
            $m = $msg;
            $msgs[] = $m;
        }

		$arr = array(
            'status' => $this->status,
            'success' => (int)!$this->isError(),
            'messages' => $msgs,
            'data' => (is_array($this->data) ? $this->data : (!empty($this->data) ? array($this->data) : array()))
        );
        $res = $this->toXml($arr);
        return $res;
	}

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @param array $data
     * @param string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    private function toXml($data, $rootNodeName = 'response', $xml=null)
    {
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if (ini_get('zend.ze1_compatibility_mode') == 1)
        {
            ini_set ('zend.ze1_compatibility_mode', 0);
        }

        if ($xml == null)
        {
            $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
        }

        // loop through the data passed in.
        foreach($data as $key => $value)
        {
            // no numeric keys in our xml please!
            if (is_numeric($key))
            {
                // make string key...
                $key = "item"/*. (string) $key*/;

                if($xml->getName() === 'messages'){
                    $key = 'message';
                }else
                if($xml->getName() === 'errors'){
                    $key = 'error';
                }
            }

            // replace anything not alpha numeric
            $key = \Api\Service\Util\Utils::normalizeName($key);

            // if there is another array found recrusively call this function
            if (is_array($value) || (!is_string($value) && !is_numeric($value) && is_array((array)$value)))
            {
                $node = $xml->addChild($key);
                // recrusive call.
                if(!is_array($value)){
                    $value = (array)$value;
                }
                $this->toXml($value, $rootNodeName, $node);
            }
            else
            {
                // add single node.
                $value = htmlentities($value);
                $xml->addChild($key,$value);
            }

        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }

}