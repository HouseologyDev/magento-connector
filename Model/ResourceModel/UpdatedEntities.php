<?php

namespace B2bapp\UpdateHandler\Model\ResourceModel;

class UpdatedEntities extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct(){
        $this->_init("b2bapp_updated_entities", "id");
    }
}