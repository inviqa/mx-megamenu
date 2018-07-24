<?php

namespace MX\MegaMenu\Api;

use Magento\Framework\Exception\LocalizedException;

interface ImportHandlerInterface
{
    /**
     * Import from file
     *
     * @param mixed $file
     * @throws LocalizedException
     */
    public function importFromFile($file);
}
