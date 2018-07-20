<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use MX\MegaMenu\Model\MenuFactory;
use MX\MegaMenu\Api\MenuRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

class Save extends MenuController
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var MenuFactory
     */
    private $menuFactory;

    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param DataPersistorInterface $dataPersistor
     * @param MenuFactory|null $menuFactory
     * @param MenuRepositoryInterface|null $menuRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        DataPersistorInterface $dataPersistor,
        MenuFactory $menuFactory = null,
        MenuRepositoryInterface $menuRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->menuFactory = $menuFactory ?: ObjectManager::getInstance()->get(MenuFactory::class);
        $this->menuRepository = $menuRepository ?: ObjectManager::getInstance()->get(MenuRepositoryInterface::class);

        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     */
    public function execute()
    {
        $response = [
            'status' => false,
            'url' => $this->getUrl('*/*/')
        ];

        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['menu_id'])) {
                $data['menu_id'] = null;
            }

            $this->coreRegistry->register('mx_megamenu_menu_items', $data['items']);

            /** @var \MX\MegaMenu\Model\Menu $model */
            $model = $this->menuFactory->create();

            $id = $this->getRequest()->getParam('menu_id');
            if ($id) {
                try {
                    $model = $this->menuRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This menu no longer exists.'));
                    $response['url'] = $resultRedirect->setPath('*/*/');

                    return $this->sendHtmlResponse($response);
                }
            }

            $model->setData($data);

            try {
                $this->menuRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the menu.'));
                $this->dataPersistor->clear('mx_megamenu');
                if ($this->getRequest()->getParam('back')) {
                    $response['url'] = $resultRedirect->setPath('*/*/edit', ['menu_id' => $model->getId()]);

                    return $this->sendHtmlResponse($response);
                }

                $response['status'] = true;
                $response['url'] = $resultRedirect->setPath('*/*/');

                return $this->sendHtmlResponse($response);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the menu.'));
            }

            $this->dataPersistor->set('mx_megamenu', $data);

            $response['url'] = $resultRedirect->setPath(
                '*/*/edit',
                [
                    'menu_id' => $this->getRequest()->getParam('menu_id')
                ]
            );
        }

        return $this->sendHtmlResponse($response);
    }
}