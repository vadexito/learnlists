<?php

namespace VxoReview\Service;

interface ReviewServiceInterface 
{
    public function insert($review);
    public function update($review);
    public function fetchAll();
    public function fetchById($id);
    public function delete($entityId);
    public function getCount();
    public function getRepository();
    public function setEntityClass($entityClass);
    public function getEntityClass();
    public function fetchByReviewedItem($reviewedItemId);
}