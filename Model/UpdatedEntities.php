<?php

namespace B2bapp\UpdateHandler\Model;


class UpdatedEntities extends \Magento\Framework\Model\AbstractModel implements UpdatedEntitiesInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'updated_entities_post';

    protected $_cacheTag = 'updated_entities_post';

    protected $_eventPrefix = 'updated_entities_post';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct(){
        $this->_init("B2bapp\UpdateHandler\Model\ResourceModel\UpdatedEntities");
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}