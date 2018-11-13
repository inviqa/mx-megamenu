<?php

namespace MX\MegaMenu\Block;

use MX\MegaMenu\Model\Menu;
use MX\MegaMenu\Model\Menu\Item as MenuItem;
use MX\MegaMenu\Model\MenuRepository;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * TopMenu
 *
 * @package MX\MegaMenu\Block
 *
 * @method string getBlockId()
 */
class TopMenu extends Template
{
    const CACHE_KEY = 'MX_MEGAMENU_TOPMENU_BLOCK';

    const CHILDREN_ALIAS = 'mx.megamenu.structure.children';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Menu repository
     *
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * Construct
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param MenuRepository $menuRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        MenuRepository $menuRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
        $this->menuRepository = $menuRepository;
    }

    protected function _construct()
    {
        parent::_construct();

        $this->addData(
            [
            'cache_lifetime' => 86400,
            'cache_tags' => [Menu::CACHE_TAG]
            ]
        );
    }

    /**
     * Get key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo()
    {
        return [
            self::CACHE_KEY,
            $this->storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            'template' => $this->getTemplate(),
            $this->getData('menu_id')
        ];
    }

    /**
     * Get structure
     *
     * @return array|false
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStructure()
    {
        $storeId = $this->storeManager->getStore()->getId();
        /** @var \MX\MegaMenu\Model\Menu $menu */
        $menu = $this->menuRepository->getByStoreId($storeId);
        if ($menu->getMenuId() && $menu->isActive()) {
            return $menu->getProcessedMenuItems();
        }

        return false;
    }

    /**
     * Render children items
     *
     * @param array $item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function renderChildren($item)
    {
        /** @var $block MX\MegaMenu\Block\TopMenu\Children */
        $block = $this->getChildBlock(self::CHILDREN_ALIAS);
        $block->setItem($item);

        return $this->getChildHtml(self::CHILDREN_ALIAS, false);
    }

    /**
     * Can show content
     *
     * @param array $item
     * @return boolean
     */
    public function canShowContent($item)
    {
        return !empty($item['header_status'])
            || !empty($item['content_status'])
            || !empty($item['leftside_status'])
            || !empty($item['rightside_status'])
            || !empty($item['footer_status'])
            || isset($item['children']);
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [Menu::CACHE_TAG . '_' . $this->getBlockId()];
    }
}