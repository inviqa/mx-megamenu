<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use Magento\Framework\Controller\ResultFactory;

class Export extends MenuController
{
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('MX Mega Menu - Export'));

        return $resultPage;
    }
}