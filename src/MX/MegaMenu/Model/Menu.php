<?php

namespace MX\MegaMenu\Model;

use MX\MegaMenu\Model\Menu\ItemFactory as MenuItemFactory;
use MX\MegaMenu\Model\Menu\Item;
use MX\MegaMenu\Api\Data\MenuInterface;
use MX\MegaMenu\Model\ResourceModel\Menu as ResourceMenu;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class Menu extends AbstractModel implements MenuInterface, IdentityInterface
{
    const CACHE_TAG = 'mx_megamenu';

    /**
     * Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    protected $_cacheTag = 'mx_megamenu';

    protected $_eventPrefix = 'mx_megamenu';

    /**
     * @var MenuItemFactory
     */
    protected $menuItemFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        MenuItemFactory $menuItemFactory,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->menuItemFactory = $menuItemFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(ResourceMenu::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getMenuId()];
    }

    /**
     * Retrieve menu id
     *
     * @return integer
     */
    public function getMenuId()
    {
        return $this->getData(self::MENU_ID);
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Retrieve link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->getData(self::LINK);
    }

    /**
     * Retrieve creation time
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Retrieve update time
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return (bool)$this->getData(self::STATUS);
    }

    /**
     * Receive page store ids
     *
     * @return mixed
     */
    public function getStores()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get sorted menu items
     *
     * @return array
     */
    public function getSortedMenuItems()
    {
        $items = $this->getMenuItems();

        usort($items, function($previous, $next) {
            return ($previous['sort_order'] <=> $next['sort_order']);
        });

        return $items;
    }

    /**
     * Receive menu items
     *
     * @return array
     */
    public function getMenuItems()
    {
        if ($this->hasData(self::MENU_ITEMS)) {
            return $this->getData(self::MENU_ITEMS);
        }

        $items = [];
        $data = $this->_registry->registry('mx_megamenu_menu_items');
        if ($data) {
            $menuItems = json_decode($data, true);
            if ($menuItems) {
                $items = $menuItems;
                $this->setData(self::MENU_ITEMS, $items);
            }
        }

        return $items;
    }

    /**
     * Get menu item ids
     *
     * @return array
     */
    public function getMenuItemIds()
    {
        $ids = [];
        $items = $this->getMenuItems();

        if (count($items)) {
            foreach ($items as $item) {
                $ids[] = $item['menu_item_id'];
            }
        }

        return $ids;
    }

    /**
     * Is active
     *
     * @return boolean
     */
    public function isActive()
    {
        $status = $this->getStatus();

        return $status == self::STATUS_ENABLED;
    }

    /**
     * Set ID
     *
     * @param integer $id
     * @return MenuInterface
     */
    public function setMenuId($id)
    {
        return $this->setData(self::MENU_ID, $id);
    }

    /**
     * Set Stores
     *
     * @param int|array $storeId
     * @return $this
     */
    public function setStores($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set NAME
     *
     * @param string $name
     * @return MenuInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Set Link
     *
     * @param string $link
     * @return MenuInterface
     */
    public function setLink($link)
    {
        return $this->setData(self::NAME, $link);
    }

    /**
     * Set creation time
     *
     * @param string $createdAt
     * @return MenuInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Set update time
     *
     * @param string $updatedAt
     * @return MenuInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Set status
     *
     * @param bool|int $status
     * @return MenuInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Set menu items
     *
     * @param array $items
     * @return $this
     */
    public function setMenuItems(&$items)
    {
        $menuItem = $this->menuItemFactory->create();
        foreach ($items as $id => $item) {
            foreach ($item as $name => $value) {
                if ($menuItem->needEncode($name)) {
                    $value = $menuItem->encodeContent($value);

                    $items[$id][$name] = $menuItem->encodeSpecialCharacters($value);
                }
            }
        }

        return $this->setData(self::MENU_ITEMS, $items);
    }

    /**
     * Add special items - e.g. real category name based on the category id
     *
     * @param array $items
     */
    public function addSpecialMenuItems(&$items)
    {
        $menuItem = $this->menuItemFactory->create();
        foreach ($items as $id => $item) {
            $items[$id]['category_name'] = $menuItem->getCategoryName($item);
        }
    }

    /**
     * Remove special items before save
     *
     * @param array $items
     */
    public function removeSpecialMenuItems(&$items)
    {
        foreach ($items as $id => $item) {
            unset($items[$id]['category_name']);
        }
    }

    /**
     * Prepare menu's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Get Processed Menu Items
     *
     * @return array
     */
    public function getProcessedMenuItems()
    {
        $result = [];
        foreach ($this->getSortedMenuItems() as $item) {
            $itemId = $item['menu_item_id'];
            $parentId = $item['menu_item_parent_id'];

            $menuItem = $this->menuItemFactory->create();
            $menuItemData = $menuItem->getItemData($item);

            if ($parentId == 0) {
                $result[$itemId] = $menuItemData;
            } else {
                // 2nd level (1st child)
                if (isset($result[$parentId])) {
                    $menuItemData['level'] = Item::LEVEL_1;
                    $result[$parentId]['children'][$itemId] = $menuItemData;
                } else {
                    // 3rd level (2nd child)
                    $menuItemData['level'] = Item::LEVEL_2;
                    $this->setChildrenItems($result, $item, $menuItemData);
                }
            }
        }

        return $result;
    }

    /**
     * Set lower level children
     *
     * @param array $result
     * @param array $item
     * @param array $menuItemData
     */
    protected function setChildrenItems(&$result, $item, $menuItemData)
    {
        $parentId = $item['menu_item_parent_id'];
        $itemId = $item['menu_item_id'];

        foreach ($result as $id => $child) {
            if (isset($child['children'])) {
                foreach ($child['children'] as $i => $ch) {
                    if ($i == $parentId) {
                        $result[$id]['children'][$i]['wrapper'] = true; // Add wrapper for 3rd level items
                        $result[$id]['children'][$i]['children'][$itemId] = $menuItemData;
                    }
                }
            }
        }
    }
}