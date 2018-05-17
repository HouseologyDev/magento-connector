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
     * @return string json array of updated products ids and skus
     */
    public function get()
    {
        $model = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('\Magento\Framework\App\ResourceConnection');

        $connection = $model->getConnection();

        $select = $connection->select()
            ->from(
                ['o' => $model->getTableName('b2bapp_updated_entities')],
                ['id','sku', 'action']
            )
            ->where('status=1');

        $result = $connection->fetchAll($select);
        return json_encode($result);
    }

    /**
     * Return the updated entities.
     *
     * @api
     * @param string[] array of ids
     * @return string test string
     */
    public function updatestatus($product_ids)
    {
        foreach($product_ids as $id) {
            try {
                $model = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('\Magento\Framework\App\ResourceConnection');

                $connection = $model->getConnection();

                $bind = array('id' => $id);
                $select = $connection->select()
                    ->from(
                        ['o' => $model->getTableName('b2bapp_updated_entities')]
                    )
                    ->where('id = :id');
                $row = $connection->fetchOne($select, $bind);
                
                if($row) {
                    $model = \Magento\Framework\App\ObjectManager::getInstance()
                    ->create('B2bapp\UpdateHandler\Model\UpdatedEntities');

                    $model->load($id, 'id');

                    $model->addData([
                        "updated_at" => (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                        "status" => false
                    ]);
                    $model->save();
                }
            } catch (Exception $e) {
                return -1;
            }
        }
        return 0;
    }
}
