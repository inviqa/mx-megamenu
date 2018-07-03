<?php

namespace MX\MegaMenu\Model\ResourceModel\Menu;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'menu_id';
    protected $_eventPrefix = 'mx_megamenu_collection';
    protected $_eventObject = 'menu_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'MX\MegaMenu\Model\Menu',
            'MX\MegaMenu\Model\ResourceModel\Menu'
        );
    }

}