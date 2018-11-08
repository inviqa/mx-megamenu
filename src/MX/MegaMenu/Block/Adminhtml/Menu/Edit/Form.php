<?php

namespace MX\MegaMenu\Block\Adminhtml\Menu\Edit;

use MX\MegaMenu\Model\Menu;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Catalog\Model\CategoryFactory;

/**
 * Edit Form
 *
 * @TODO Fix deprecated stuff. It must be fixed before the next major upgrade (2.3)
 *
 * @package MX\MegaMenu\Block\Adminhtml\Menu\Edit
 *
 * @method void setId(string $id)
 * @method void setTitle(string $title)
 */
class Form extends Generic
{
    /**
     * @var Config
     */
    protected $wysiwygConfig;

    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var array
     */
    protected $contentChooserOptions = [
        'wysiwyg' => 'Content',
        'category' => 'Category'
    ];

    /**
     * @var array
     */
    protected $categoryTypeChooserOptions = [
        'show' => 'Show children always',
        'hide' => 'Hide children always',
        'toggle' => 'Toggle children'
    ];

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        CategoryFactory $categoryFactory,
        array $data = []
    ) {
        $this->wysiwygConfig = $wysiwygConfig;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('wysiwyg_form');
        $this->setTitle(__('Edit Menu Item'));
    }

    protected function _prepareForm()
    {
        $itemId = $this->getRequest()->getParam('item_id', 0);

        $form = $this->_formFactory->create(
            ['data' =>
                [
                    'id' => 'edit_form',
                    'enctype' => 'multipart/form-data',
                    'action' => $this->getData('action'),
                    'method' => 'post'
                ]
            ]
        );

        $general = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General'),
                'class' => 'fieldset-wide'
            ]
        );
        $general->addField(
            'general_item_id',
            'hidden',
            [
                'name' => 'menu_item_id',
                'value' => $itemId
            ]
        );
        $general->addField(
            'general_status',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'required' => false,
                'name' => 'status',
                'value' => Menu::STATUS_DISABLED
            ]
        );
        $general->addField(
            'general_name',
            'text',
            [
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => false,
                'name' => 'name'
            ]
        );
        $general->addField(
            'general_link',
            'text',
            [
                'label' => __('Link'),
                'title' => __('Link'),
                'required' => false,
                'name' => 'link'
            ]
        );
        $general->addField(
            'general_custom_class',
            'text',
            [
                'label' => __('Custom Classes'),
                'title' => __('Custom Classes'),
                'required' => false,
                'name' => 'custom_class'
            ]
        );

        $header = $form->addFieldset(
            'header_fieldset',
            [
                'legend' => __('Header'),
                'class' => 'fieldset-wide'
            ]
        );
        $header->addField(
            'header_status',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => false,
                'name' => 'header_status',
                'value' => Menu::STATUS_DISABLED
            ]
        );
        $header->addField(
            $this->getFormFieldId('header_wysiwyg', $itemId),
            'editor',
            [
                'name' => 'header_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:10em',
                'required' => false,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );

        $content = $form->addFieldset(
            'content_fieldset',
            [
                'legend' => __('Main Content'),
                'class' => 'fieldset-wide'
            ]
        );
        $content->addField(
            'content_status',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => false,
                'name' => 'content_status',
                'value' => Menu::STATUS_DISABLED
            ]
        );
        $content->addField(
            'content_chooser',
            'select',
            [
                'label' => __('Content Type'),
                'title' => __('Content Type'),
                'required' => true,
                'options' => $this->getContentChooserOptions(),
                'name' => 'content_type',
            ]
        );
        $content->addField(
            $this->getFormFieldId('content_wysiwyg', $itemId),
            'editor',
            [
                'name' => 'content_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:10em',
                'required' => false,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );
        $content->addField(
            'content_category',
            'MX\MegaMenu\Data\Form\Element\Chooser\Category',
            [
                'label' => __('Category'),
                'title' => __('Category'),
                'required' => false,
                'name' => 'content_category',
                'button_label' => __('Select Category...')
            ]
        );
        $content->addField(
            'content_category_type',
            'select',
            [
                'label' => __('Category Type'),
                'title' => __('Category Type'),
                'required' => false,
                'options' => $this->getCategoryTypeChooserOptions(),
                'name' => 'content_category_type',
            ]
        );
        $content->addField(
            'remove_category_anchor',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Remove Category Link'),
                'title' => __('Remove Category Link'),
                'required' => false,
                'name' => 'remove_category_anchor',
                'value' => Menu::STATUS_DISABLED
            ]
        );

        $leftside = $form->addFieldset(
            'left_side_fieldset',
            [
                'legend' => __('Left Side'),
                'class' => 'fieldset-wide'
            ]
        );
        $leftside->addField(
            'leftside_status',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => false,
                'name' => 'leftside_status',
                'value' => Menu::STATUS_DISABLED
            ]
        );
        $leftside->addField(
            $this->getFormFieldId('left_side_wysiwyg', $itemId),
            'editor',
            [
                'name' => 'leftside_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:10em',
                'required' => false,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );

        $rightside = $form->addFieldset(
            'right_side_fieldset',
            [
                'legend' => __('Right Side'),
                'class' => 'fieldset-wide'
            ]
        );
        $rightside->addField(
            'rightside_status',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => false,
                'name' => 'rightside_status',
                'value' => Menu::STATUS_DISABLED
            ]
        );
        $rightside->addField(
            $this->getFormFieldId('right_side_wysiwyg', $itemId),
            'editor',
            [
                'name' => 'rightside_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:10em',
                'required' => false,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );

        $footer = $form->addFieldset(
            'footer_fieldset',
            [
                'legend' => __('Footer'),
                'class' => 'fieldset-wide'
            ]
        );
        $footer->addField(
            'footer_status',
            'MX\MegaMenu\Data\Form\Element\Toggle',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => false,
                'name' => 'footer_status',
                'value' => Menu::STATUS_DISABLED
            ]
        );
        $footer->addField(
            $this->getFormFieldId('footer_wysiwyg', $itemId),
            'editor',
            [
                'name' => 'footer_content',
                'label' => __('Content'),
                'title' => __('Content'),
                'style' => 'height:10em',
                'required' => false,
                'config' => $this->wysiwygConfig->getConfig()
            ]
        );

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get unique form field id
     *
     * @param string $field
     * @param integer $itemId
     * @return string
     */
    protected function getFormFieldId($field, $itemId)
    {
        return $field . '_' . $itemId;
    }

    /**
     * Get content chooser options
     *
     * @return array
     */
    protected function getContentChooserOptions()
    {
        return $this->contentChooserOptions;
    }

    /**
     * Get categor type chooser options
     *
     * @return array
     */
    protected function getCategoryTypeChooserOptions()
    {
        return $this->categoryTypeChooserOptions;
    }
}