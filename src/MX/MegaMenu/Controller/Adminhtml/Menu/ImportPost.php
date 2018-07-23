<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use MX\MegaMenu\Model\Menu\ImportHandlerFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Json\Helper\Data as JsonDataHelper;

class ImportPost extends MenuController
{
    /**
     * @var ImportHandlerFactory
     */
    protected $importHandler;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @oaram JsonDataHelper $jsonDataHelper
     * @param ImportHandlerFactory $importHandler
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        JsonDataHelper $jsonDataHelper,
        ImportHandlerFactory $importHandler
    ) {
        $this->importHandler = $importHandler;

        parent::__construct($context, $coreRegistry, $jsonDataHelper);
    }

    public function execute()
    {
        try {
            $file = $this->getRequest()->getFiles('file');

            if ($file) {
                /** @var $importHandler \MX\MegaMenu\Model\Menu\ImportHandler */
                $importHandler = $this->importHandler->create();
                $importHandler->importFromFile($file);

                $this->messageManager->addSuccessMessage(__('You successfully imported the menu items.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while importing the menu items.'));
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('mx_megamenu/menu/import');
    }
}