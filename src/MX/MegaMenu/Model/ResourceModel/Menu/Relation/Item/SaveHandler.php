<?php

namespace MX\MegaMenu\Model\ResourceModel\Menu\Relation\Item;

use MX\MegaMenu\Model\ResourceModel\Menu;
use MX\MegaMenu\Api\Data\MenuInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 *
 * @TODO Fix deprecated getEntityConnection(). It must be fixed before the next major upgrade (2.3)
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Menu
     */
    protected $resourceMenu;

    /**
     * @param MetadataPool $metadataPool
     * @param Menu $resourceMenu
     */
    public function __construct(
        MetadataPool $metadataPool,
        Menu $resourceMenu
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceMenu = $resourceMenu;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(MenuInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldItems = $this->resourceMenu->lookupMenuItems((int)$entity->getMenuId());
        $oldItemIds = array_keys($oldItems);
        $newItemIds = (array)$entity->getMenuItemIds();

        $table = $this->resourceMenu->getTable('mx_megamenu_item');

        // Delete old items
        $delete = array_diff($oldItemIds, $newItemIds);
        if (!empty($delete)) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'menu_item_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        // Insert new items
        $insert = array_diff($newItemIds, $oldItemIds);
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $itemId) {
                $data[$itemId] = [
                    $linkField => (int)$entity->getData($linkField),
                    'menu_item_id' => null,
                ];

                foreach ($entity->getMenuItems() as $menuItem) {
                    if ($menuItem['menu_item_id'] ==  $itemId) {
                        foreach ($menuItem as $name => $value) {
                            if ($name !== 'menu_item_id') {
                                $data[$itemId][$name] = $value;
                            }
                        }
                    }
                }
            }
            $connection->insertMultiple($table, $data);
        }

        // Update existing items
        $menuItems = $entity->getMenuItems();
        if (count($insert) == 0 && count($menuItems) > 0) {
            foreach ($menuItems as $menuItem) {
                $where = [
                    $linkField . ' = ?' => (int)$entity->getData($linkField),
                    'menu_item_id = (?)' => $menuItem['menu_item_id'],
                ];
                unset($menuItem['menu_id']);
                unset($menuItem['menu_item_id']);
                $connection->update($table, $menuItem, $where);
            }
        }

        return $entity;
    }
}
