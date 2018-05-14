<?php

namespace B2bapp\UpdateHandler\ResourceModel\UpdatedEntities;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct(){
        $this->_init("B2bapp\UpdateHandler\Model\UpdatedEntities","B2bapp\UpdateHandler\Model\ResourceModel\UpdatedEntities");
    }
}