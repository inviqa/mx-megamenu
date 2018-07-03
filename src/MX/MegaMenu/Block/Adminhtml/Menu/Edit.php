<?php

namespace MX\MegaMenu\Block\Adminhtml\Menu;

use MX\MegaMenu\Model\MenuFactory;
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
     * @param Context $context
     * @param Registry $registry
     * @param ManagerInterface $messageManager
     * @param MenuFactory $menuFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ManagerInterface $messageManager,
        MenuFactory $menuFactory,
        array $data = []
    )
    {
        $this->coreRegistry = $registry;
        $this->messageManager = $messageManager;
        $this->menuFactory = $menuFactory;
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
        $menuModel = $this->coreRegistry->registry('mx_megamenu_menu');

        if ($menuModel) {
            return $this->encodeItems($menuModel->getMenuItems());
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
    protected function encodeItems($items)
    {
        $encodedString = json_encode($items);
        $encodedString = str_replace('\\\\', '\\\\\\\\', $encodedString);

        return  str_replace('\\"' , '\\\\"' , $encodedString);
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