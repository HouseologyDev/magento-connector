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
     * @param string entity type
     * @return string json array of updated products ids and skus
     */
    public function get($entity)
    {
        $validEntities = ['product', 'category'];

        if (!in_array($entity, $validEntities)) {
            return '[]';
        }

        $model = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('\Magento\Framework\App\ResourceConnection');

        $connection = $model->getConnection();

        $bind = array('entity' => $entity);
        $select = $connection->select()
            ->from(
                ['o' => $model->getTableName('b2bapp_updated_entities')],
                ['id','entity_id','sku', 'action']
            )
            ->where('entity = :entity AND status = 1');

        $result = $connection->fetchAll($select, $bind);
        return json_encode($result);
    }

    /**
     * Return the updated entities.
     *
     * @api
     * @param string entity type
     * @param string[] array of entity ids
     * @return string test string
     */
    public function updatestatus($entity, $entity_ids)
    {
        $validEntities = ['product', 'category'];

        if (!in_array($entity, $validEntities)) {
            return -1;
        }

        foreach($entity_ids as $entitytId) {
            try {
                $model = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('\Magento\Framework\App\ResourceConnection');

                $connection = $model->getConnection();

                $bind = array('entity' => $entity, 'entity_id' => $entitytId);
                $select = $connection->select()
                    ->from(
                        ['o' => $model->getTableName('b2bapp_updated_entities')]
                    )
                    ->where('entity = :entity AND entity_id = :entity_id');

                $id = $connection->fetchOne($select, $bind);
                
                if($id) {
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
