<?php

namespace MX\MegaMenu\Block\TopMenu;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * TopMenu Children
 *
 * @package MX\MegaMenu\Block\TopMenu
 */
class Children extends Template
{
    const LEVEL_1 = 1;
    const LEVEL_2 = 2;
    const LEVEL_1_DEPTH = 6;
    const LEVEL_2_DEPTH = 3;

    /**
     * @var array
     */
    protected $item;

    /**
     * Level definitions
     *
     * @var array
     */
    protected $levels = [
        self::LEVEL_1_DEPTH => self::LEVEL_1,
        self::LEVEL_2_DEPTH => self::LEVEL_2
    ];

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
     * @return integer|mixed
     */
    public function getItemLevel($item)
    {
        $arrayDepth = $this->getArrayDepth($item);
        if ($arrayDepth > 1 && isset($this->levels[$arrayDepth])) {
            return $this->levels[$arrayDepth];
        }

        return self::LEVEL_1;
    }

    /**
     * Get array depth
     *
     * @param array $array
     * @return integer
     */
    protected function getArrayDepth(array $array)
    {
        $maxDepth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = $this->getArrayDepth($value) + 1;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }
}