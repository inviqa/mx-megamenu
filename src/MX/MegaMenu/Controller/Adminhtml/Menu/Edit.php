<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use MX\MegaMenu\Model\Menu;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Edit extends MenuController
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
        $id = $this->getRequest()->getParam('menu_id');
        if ($id) {
            $menuModel = $this->_objectManager->create(Menu::class);
            $menuModel->load($id);
            if (!$menuModel->getId()) {
                $this->messageManager->addErrorMessage(__('The menu with this specific ID does not exist.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }

            // Register data to pass through to the Form
            $this->coreRegistry->register('mx_megamenu_menu', $menuModel);
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('MX Mega Menu - Edit Menu')));

        return $resultPage;
    }
}