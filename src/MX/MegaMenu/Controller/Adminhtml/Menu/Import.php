<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Import extends MenuController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context, $registry);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('MX Mega Menu - Import')));

        return $resultPage;
    }
}