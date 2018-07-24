<?php

namespace MX\MegaMenu\Block\Adminhtml\Menu;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;

/**
 * Class Export
 *
 * @package MX\MegaMenu\Block\Adminhtml\Menu
 */
class Export extends Container
{
    /**
     * Initialize export block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'menu_id';
        $this->_controller = 'adminhtml_menu';
        $this->_blockGroup = 'MX_MegaMenu';

        parent::_construct();

        $this->removeButton('reset');
        $this->removeButton('delete');
        $this->removeButton('save');
    }

    /**
     * Get Export Url
     *
     * @return string
     */
    public function getExportUrl()
    {
        return $this->getUrl('mx_megamenu/menu/exportpost');
    }
}