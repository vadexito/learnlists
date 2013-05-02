<?php

namespace LrnlSearch\Document;

use LrnlListquests\Entity\Listquest;

interface ListquestDocumentInterface
{
    public function setData($id,Listquest $list);
}