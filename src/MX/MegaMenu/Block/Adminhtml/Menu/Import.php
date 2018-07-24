<?php

namespace MX\MegaMenu\Block\Adminhtml\Menu;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;

/**
 * Class Import
 *
 * @package MX\MegaMenu\Block\Adminhtml\Menu
 */
class Import extends Container
{
    /**
     * Initialize import block
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
     * Get Import Url
     *
     * @return string
     */
    public function getImportUrl()
    {
        return $this->getUrl('mx_megamenu/menu/importpost');
    }
}