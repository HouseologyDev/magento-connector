<?php

namespace B2bapp\UpdateHandler\Model;

use B2bapp\UpdateHandler\Api\UpdateHandlerInterface;
use \Magento\Framework\EntityManager\EntityManager;

class UpdateHandler implements UpdateHandlerInterface
{

    /**
     * Return the updated entities.
     *
     * @api
     * @return string test string
     */
    public function get()
    {
        $model = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('\Magento\Framework\App\ResourceConnection');

        $connection = $model->getConnection();

        $select = $connection->select()
            ->from(
                ['o' => $model->getTableName('b2bapp_updated_entities')],
                ['sku']
            )
            ->where('status=1');

        $result = $connection->fetchAll($select);
        return json_encode($result);
    }
}
