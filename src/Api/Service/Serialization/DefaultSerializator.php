<?php
/**
 * @author a.itsekson
 * @createdAt: 26.11.2015 18:11
 */

namespace Api\Service\Serialization;


use Api\Service\AnnotatedServiceInterface;
use Api\Service\Util\IArrayExchange;

class DefaultSerializator implements SerializatorInterface
{

    /**
     * @var IArrayExchange|null
     */
    private $entity = null;

    private $neededColumns = null;

    public function __construct(IArrayExchange $entity, $neededColumns)
    {
        $this->entity = $entity;
        $this->neededColumns = $neededColumns;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $original = $this->entity->toArray();

        $columns = [];
        if(is_array($this->neededColumns)){
            $columns = $this->neededColumns;
        }else if($this->neededColumns instanceof AnnotatedServiceInterface){
            try{
                $helper = $this->neededColumns->getAnnotationsHelper();
                $columns = $this->neededColumns->getColumns($helper);
            }catch (\InvalidArgumentException $ex){
                $columns = $this->neededColumns;
            }
        }

        $res = $original;
        foreach ($res as $index => $r) {
            if(!in_array($r, $columns)){
                unset($res[$index]);
            }
        }
        return $res;
    }


}