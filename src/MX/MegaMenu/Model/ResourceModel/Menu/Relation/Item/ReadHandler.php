<?php

namespace MX\MegaMenu\Model\ResourceModel\Menu\Relation\Item;

use MX\MegaMenu\Model\ResourceModel\Menu;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Menu
     */
    protected $resourceMenu;

    /**
     * @param Menu $resourceMenu
     */
    public function __construct(
        Menu $resourceMenu
    ) {
        $this->resourceMenu = $resourceMenu;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getMenuId()) {
            $items = $this->resourceMenu->lookupMenuItems((int)$entity->getMenuId());
            $entity->addSpecialMenuItems($items);
            $entity->setMenuItems($items);
        }
        return $entity;
    }
}
