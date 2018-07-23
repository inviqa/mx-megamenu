<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use MX\MegaMenu\Model\MenuFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Json\Helper\Data as JsonDataHelper;

class Delete extends MenuController
{
    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param JsonDataHelper $jsonDataHelper
     * @param MenuFactory $menuFactory
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
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('menu_id');
        if ($id) {
            $menuModel = $this->menuFactory->create();
            $menuModel->load($id);

            if (!$menuModel->getMenuId()) {
                $this->messageManager->addErrorMessage(__('The menu with this specific ID does not exist.'));
            } else {
                $menuModel->delete();
                $this->messageManager->addSuccessMessage(__('The menu with this specific ID was successfully deleted.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}