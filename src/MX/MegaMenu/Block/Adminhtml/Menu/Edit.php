<?php

namespace MX\MegaMenu\Block\Adminhtml\Menu;

use MX\MegaMenu\Model\MenuFactory;
use MX\MegaMenu\Model\Menu\ItemFactory as MenuItemFactory;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Edit
 *
 * @TODO Fix deprecated stuff. It must be fixed before the next major upgrade (2.3)
 *
 * @package MX\MegaMenu\Block\Adminhtml\Menu
 */
class Edit extends Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * @var MenuItemFactory
     */
    protected $menuItemFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ManagerInterface $messageManager
     * @param MenuFactory $menuFactory
     * @param MenuItemFactory $menuItemFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ManagerInterface $messageManager,
        MenuFactory $menuFactory,
        MenuItemFactory $menuItemFactory,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->messageManager = $messageManager;
        $this->menuFactory = $menuFactory;
        $this->menuItemFactory = $menuItemFactory;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'menu_id';
        $this->_controller = 'adminhtml_menu';
        $this->_blockGroup = 'MX_MegaMenu';

        parent::_construct();
    }

    /**
     * Get Menu Json String
     *
     * @return string
     */
    public function getMenuJson()
    {
        /** @var $menuModel \MX\MegaMenu\Model\Menu|null */
        $menuModel = $this->coreRegistry->registry('mx_megamenu_menu');

        if ($menuModel) {
            $items = $menuModel->getSortedMenuItems();
            return $this->encodeItems($items);
        }

        return '';
    }

    /**
     * Get Edit form Url
     *
     * @return string
     */
    public function getEditFormUrl()
    {
        return $this->getUrl('mx_megamenu/menu/form');
    }

    /**
     * Get Save Form Url
     *
     * @return string
     */
    public function getSaveFormUrl()
    {
        return $this->getUrl('mx_megamenu/menu/save');
    }

    /**
     * Get encoded string - useful for widget encoded strings
     *
     * @param array $items
     * @return mixed
     */
    protected function encodeItems(&$items)
    {
        $menuItem = $this->menuItemFactory->create();
        foreach ($items as &$item) {
            foreach ($item as $name => $value) {
                if ($menuItem->needEncode($name)) {
                    $value = $menuItem->decodeContent($value);
                    $value = $menuItem->encodeSpecialCharacters($value);
                    $item[$name] = base64_encode($value);
                }
            }
        }

        return json_encode($items);
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}