<?php
namespace LrnlListquests\Entity;

interface ListquestInterface
{
    public function getId();
    public function getTitle();
    public function getPictureId();
    public function getCategory();
}