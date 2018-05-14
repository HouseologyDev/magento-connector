<?php

namespace B2bapp\UpdateHandler\Api\Data;

interface UpdatedEntitiesInterface
{
    /**
     * Product id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Entity type
     *
     * @return string|null
     */
    public function getEntity();

    /**
     * Set Entity type
     *
     * @param string $entity
     * @return $this
     */
    public function setEntity($entity);

    /**
     * Updated at
     *
     * @return date|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     *
     * @param date $updated_at
     * @return $this
     */
    public function setUpdatedAt($updated_at);

    /**
     * Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);
}