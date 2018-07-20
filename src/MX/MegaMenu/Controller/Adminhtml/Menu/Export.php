<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use MX\MegaMenu\Model\Menu;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Export extends MenuController
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
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
        $resultPage->getConfig()->getTitle()->prepend((__('MX Mega Menu - Export')));

        return $resultPage;
    }
}