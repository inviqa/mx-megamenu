<?php

namespace MX\MegaMenu\Model;

use MX\MegaMenu\Api\Data\MenuInterface;
use MX\MegaMenu\Api\MenuRepositoryInterface;
use MX\MegaMenu\Model\ResourceModel\Menu as ResourceMenu;
use MX\MegaMenu\Model\ResourceModel\Menu\Collection as MenuCollection;
use MX\MegaMenu\Model\ResourceModel\Menu\CollectionFactory as MenuCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class MenuRepository implements MenuRepositoryInterface
{
    /**
     * @var ResourceMenu
     */
    protected $resource;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var MenuCollectionFactory
     */
    protected $menuCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ResourceMenu $resource
     * @param MenuFactory $menuFactory
     * @param MenuCollectionFactory $menuCollectionFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceMenu $resource,
        MenuFactory $menuFactory,
        MenuCollectionFactory $menuCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->menuFactory = $menuFactory;
        $this->menuCollectionFactory = $menuCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Save Menu data
     *
     * @param MenuInterface $menu
     * @return Menu
     * @throws CouldNotSaveException
     */
    public function save(MenuInterface $menu)
    {
        if (empty($menu->getStores())) {
            $menu->setStores($this->storeManager->getStore()->getId());
        }

        try {
            $this->resource->save($menu);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $menu;
    }

    /**
     * Load Menu data by given Menu Identity
     *
     * @param string $menuId
     * @return Menu
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($menuId)
    {
        $menu = $this->menuFactory->create();
        $this->resource->load($menu, $menuId);
        if (!$menu->getMenuId()) {
            throw new NoSuchEntityException(__('Menu with id "%1" does not exist.', $menuId));
        }

        return $menu;
    }

    /**
     * Load Menu data by given Store Id
     *
     * @param $storeId
     * @return Menu
     */
    public function getByStoreId($storeId)
    {
        $menu = $this->menuFactory->create();
        $menu->setStores($storeId);
        $this->resource->load($menu, $storeId, 'store_id');

        return $menu;
    }

    /**
     * Get all items
     *
     * @return MenuCollection
     * @throws NoSuchEntityException
     */
    public function getAllItems()
    {
        $collection = $this->menuCollectionFactory->create();

        return $collection->getItems();
    }

    /**
     * Delete Menu
     *
     * @param MenuInterface $menu
     * @return boolean
     * @throws CouldNotDeleteException
     */
    public function delete(MenuInterface $menu)
    {
        try {
            $this->resource->delete($menu);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete Menu by given Menu Identity
     *
     * @param string $menuId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($menuId)
    {
        return $this->delete($this->getById($menuId));
    }
}
