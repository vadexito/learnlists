<?php

namespace VxoUtils\Entity;

abstract class EntityAbstract
{
 
    public function __get($item)
    {
        $getterName = 'get'.ucfirst($item);
        if (method_exists($this,$getterName))
        {
            return $this->$getterName();
        }
        return $this->$item;
    }
    
    public function __set($item,$value)
    {
        $setterName = 'set'.ucfirst($item);
        if (method_exists($this,$setterName))
        {
            $this->$setterName($value);
        }
        else
        {
            $this->$item = $value;
        }
    }
}

    

   