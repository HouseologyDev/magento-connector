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
     * @return string test string
     */
    public function get();
}