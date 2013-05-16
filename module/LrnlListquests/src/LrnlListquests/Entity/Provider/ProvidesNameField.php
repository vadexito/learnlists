<?php

namespace LrnlListquests\Entity\Provider;

trait ProvidesNameField
{
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
}