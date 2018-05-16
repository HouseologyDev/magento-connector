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
     * @return string json array of updated products ids and skus
     */
    public function get();

    /**
     * Return the updated entities.
     *
     * @api
     * @param string[] array of ids
     * @return string test string
     */
    public function updatestatus($product_ids);
}