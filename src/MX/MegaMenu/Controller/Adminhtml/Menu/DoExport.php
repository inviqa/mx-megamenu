<?php

namespace MX\MegaMenu\Controller\Adminhtml\Menu;

use MX\MegaMenu\Controller\Adminhtml\Menu as MenuController;
use MX\MegaMenu\Model\MenuFactory;
use MX\MegaMenu\Api\MenuRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;

class DoExport extends MenuController
{
    /**
     * @var MenuRepositoryInterface
     */
    private $menuRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param MenuFactory|null $menuFactory
     * @param MenuRepositoryInterface|null $menuRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MenuFactory $menuFactory = null,
        MenuRepositoryInterface $menuRepository = null
    ) {
        $this->menuFactory = $menuFactory ?: ObjectManager::getInstance()->get(MenuFactory::class);
        $this->menuRepository = $menuRepository ?: ObjectManager::getInstance()->get(MenuRepositoryInterface::class);

        parent::__construct($context, $coreRegistry);
    }

    public function execute()
    {
        $response = [
            'status' => false,
            'result' => '',
            'redirect' => $this->getUrl('mx_megamenu/menu/export')
        ];

        try {
            $result = [];
            $items = $this->menuRepository->getAllItems();

            if ($items) {
                /** @var \MX\MegaMenu\Model\Menu $model */
                $model = $this->menuFactory->create();

                foreach ($items as $item) {
                    $id = $item['menu_id'];
                    $menu = $this->menuRepository->getById($id);
                    $result[$id] = $menu->getData();

                    $response['status'] = true;
                    $response['result'] = json_encode($result);

                    $this->messageManager->addSuccessMessage(__('You successfully exported the menu items.'));
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while exporting the menu items.'));
        }

        return $this->sendHtmlResponse($response);
    }
}