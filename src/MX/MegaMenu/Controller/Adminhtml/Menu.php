<?php

namespace MX\MegaMenu\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonDataHelper;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Response\Http;

abstract class Menu extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MX_MegaMenu::menu';

    /**
     * @var JsonDataHelper
     */
    protected $jsonDataHelper;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param JsonDataHelper $jsonDataHelper
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        JsonDataHelper $jsonDataHelper
    )
    {
        $this->coreRegistry = $coreRegistry;
        $this->jsonDataHelper = $jsonDataHelper;
        parent::__construct($context);
    }

    /**
     * Send Response
     *
     * @param array $response
     *
     * @return Http
     */
    public function sendHtmlResponse($response)
    {
        return $this->getResponse()->representJson(
            $this->jsonDataHelper->jsonEncode($response)
        );
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('MX_MegaMenu::menu')
            ->addBreadcrumb(__('Mega Menu'), __('Mega Menu'))
            ->addBreadcrumb(__('Menu'), __('Menu'));
        return $resultPage;
    }
}
