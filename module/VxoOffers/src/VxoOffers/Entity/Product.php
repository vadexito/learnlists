<?php

namespace VxoOffers\Entity;


class Product extends EntityAbstract
{
    /**
     * Primary Identifier
     *
     * @var integer
     * @access protected
     */
    protected $id;

    /**
     *
     * @var string
     * @access protected
     */
    protected $name; 
    
    /**
     *
     * @var string
     * @access protected
     */
    protected $description;    
    

    /**
     *
     * @var string
     * @access protected
     */
    protected $type;
    
}