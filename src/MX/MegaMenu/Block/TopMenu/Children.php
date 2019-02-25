<?php

namespace MX\MegaMenu\Block\TopMenu;

use MX\MegaMenu\Model\Menu\Item;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * TopMenu Children
 *
 * @package MX\MegaMenu\Block\TopMenu
 */
class Children extends Template
{
    /**
     * @var array
     */
    protected $item;

    /**
     * Set item
     *
     * @param array $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * Get item
     *
     * @return array
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Get item level
     *
     * @param array $item
     * @return integer
     */
    public function getItemLevel($item)
    {
        return isset($item['level']) ? $item['level'] : Item::LEVEL_DEFAULT;
    }
}