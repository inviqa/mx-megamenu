<?php

namespace MX\MegaMenu\Block\Adminhtml\Menu\Edit\Settings;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Store\Model\System\Store;

/**
 * Settings Form
 *
 * @TODO Fix deprecated stuff. It must be fixed before the next major upgrade (2.3)
 *
 * @package MX\MegaMenu\Block\Adminhtml\Menu\Edit\Settings
 *
 * @method void setId(integer $id)
 */
class Form extends Generic
{
    const DEFAULT_STORE_ID = 0;

    /**
     * @var Store
     */
    protected $store;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $store
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $store,
        array $data = []
    ) {
        $this->store = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('settings_form');
    }

    protected function _prepareForm()
    {
        $menuModel = $this->_coreRegistry->registry('mx_megamenu_menu');
        $menuId = $this->getRequest()->getParam('menu_id', 0);

        $form = $this->_formFactory->create(
            ['data' =>
                [
                    'id' => 'settings_form',
                    'enctype' => 'multipart/form-data',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $fieldset = $form->addFieldset(
            'settings_fieldset',
            [
                'legend' => __(''),
                'class' => 'fieldset-wide'
            ]
        );

        if ($menuId) {
            $fieldset->addField(
                'menu_id',
                'hidden',
                [
                    'name' => 'menu_id',
                    'value' => $menuId
                ]
            );
        }

        $fieldset->addField(
            'enabled',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'required' => false,
                'name' => 'status',
                'value' => '1',
                'checked' => $menuModel ? $menuModel->getStatus() : ''
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'name' => 'name',
                'value' => $menuModel ? $menuModel->getName() : ''
            ]
        );

        $fieldset->addField(
            'store_id',
            'multiselect',
            [
                'name' => 'store_id',
                'label' => __('Store View'),
                'title' => __('Store View'),
                'required' => true,
                'values' => $this->store->getStoreValuesForForm(false, true),
                'value' => $menuModel ? $menuModel->getStoreId() : self::DEFAULT_STORE_ID
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}