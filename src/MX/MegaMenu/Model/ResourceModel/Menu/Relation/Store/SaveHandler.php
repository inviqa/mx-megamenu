<?php

namespace MX\MegaMenu\Model\ResourceModel\Menu\Relation\Store;

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

        $oldStores = $this->resourceMenu->lookupStoreIds((int)$entity->getMenuId());
        $newStores = (array)$entity->getStores();

        $table = $this->resourceMenu->getTable('mx_megamenu_store');

        $delete = array_diff($oldStores, $newStores);
        if (!empty($delete)) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
