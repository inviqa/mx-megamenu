<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use MX\MegaMenu\Model\MenuFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\Json\Helper\Data as JsonDataHelper;

class Edit extends MenuController
{
    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $registry
     * @param JsonDataHelper $jsonDataHelper
     */
    public function __construct(
        Context $context,
        Registry $registry,
        JsonDataHelper $jsonDataHelper,
        MenuFactory $menuFactory
    )
    {
        $this->menuFactory = $menuFactory;
        parent::__construct($context, $registry, $jsonDataHelper);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('menu_id');
        if ($id) {
            $menuModel = $this->menuFactory->create();
            $menuModel->load($id);
            if (!$menuModel->getId()) {
                $this->messageManager->addErrorMessage(__('The menu with this specific ID does not exist.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }

            // Register data to pass through to the Form
            $this->coreRegistry->register('mx_megamenu_menu', $menuModel);
        }

        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend((__('MX Mega Menu - Edit Menu')));

        return $resultPage;
    }
}