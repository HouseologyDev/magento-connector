<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace B2bapp\UpdateHandler\Api;

/**
 * Provides updated entities
 *
 * @api
 */
interface UpdateHandlerInterface
{
    /**
     * Return the updated entities.
     *
     * @api
     * @param string entity type
     * @param int limit
     * @return string json array of updated products ids and skus
     */
    public function get($entity, $limit);

    /**
     * Return the updated entities.
     *
     * @api
     * @param string entity type
     * @param string[] array of entity ids
     * @return string test string
     */
    public function updatestatus($entity, $entity_ids);
}