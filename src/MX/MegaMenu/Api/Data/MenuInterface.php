<?php

namespace MX\MegaMenu\Api\Data;

interface MenuInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const MENU_ID       = 'menu_id';
    const NAME          = 'name';
    const LINK          = 'link';
    const STATUS        = 'status';
    const CREATED_AT    = 'created_at';
    const UPDATED_AT    = 'updated_at';
    const STORE_ID      = 'store_id';
    const MENU_ITEMS    = 'menu_items';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Retrieve link
     *
     * @return string
     */
    public function getLink();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Get status
     *
     * @return bool|null
     */
    public function getStatus();

    /**
     * Set ID
     *
     * @param int $id
     * @return MenuInterface
     */
    public function setId($id);

    /**
     * Set Stores
     *
     * @param int|array $storeId
     * @return $this
     */
    public function setStores($storeId);

    /**
     * Set name
     *
     * @param string $name
     * @return MenuInterface
     */
    public function setName($name);

    /**
     * Set Link
     *
     * @param string $link
     * @return MenuInterface
     */
    public function setLink($link);

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return MenuInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return MenuInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Set is active
     *
     * @param bool|int $status
     * @return MenuInterface
     */
    public function setStatus($status);
}
