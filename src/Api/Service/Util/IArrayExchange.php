<?php

/**
 * @author a.itsekson
 */


 namespace Api\Service\Util;
 
/**
 * @desc Exchange data ro array and from array
 * @author Itsekson Alexey
 */
interface IArrayExchange {
    
    public function toArray();
    
    public function fromArray(array $data);
}

?>
