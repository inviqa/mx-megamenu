<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Model\Menu;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;

class Delete extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('menu_id');
        if ($id) {
            $menuModel = $this->_objectManager->create(Menu::class);
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