<?php
namespace LrnlCategory\Entity;

interface CategoryInterface 
{
    public function getId();
    public function setPictureId($pictureId);
    public function getPictureId();
    public function setDescription($description);
    public function getDescription();
    public function setDepth($depth);    
    public function getDepth();    
    public function setParent(Category $parent);    
    public function getParent();
    
}