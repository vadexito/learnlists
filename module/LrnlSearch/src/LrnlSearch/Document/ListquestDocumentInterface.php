<?php

namespace LrnlSearch\Document;

use LrnlListquests\Entity\Listquest;

interface ListquestDocumentInterface
{
    public function createDocumentFromListquest($id,Listquest $list);
}