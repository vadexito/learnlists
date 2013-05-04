<?php

namespace WtRatingDoctrineORM\Options;

interface WtRatingDoctrineORMOptionsInterface
{
    public function setWtRatingEntityClass($entityClass);
    public function getWtRatingEntityClass();
    public function setEnableDefaultEntities($enableDefaultEntities);
    public function getEnableDefaultEntities();
}