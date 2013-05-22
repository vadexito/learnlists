<?php
namespace VxoReview\Entity;

use ZfcUser\Entity\UserInterface as User;
use DateTime;


interface ReviewInterface 
{
    public function getId();
    public function getText();
    public function setText($text);
    public function getAuthor();
    public function setAuthor(User $author);
    public function getReviewedItem();
    public function setReviewedItem($reviewedItem);
    public function getRating();
    public function setRating($rating);
    public function getCreationDate();
    public function setCreationDate(DateTime $creationDate);
    
}