<?php

namespace MX\MegaMenu\Data\Form\Element\Chooser;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Hidden;
use Magento\Framework\Data\Form\Element\Note;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Widget\Block\Adminhtml\Widget\Chooser as WidgetChooser;
use Magento\Backend\Block\Widget\Button;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Json\EncoderInterface;

/**
 * Category Chooser element
 *
 * @method string getText()
 * @method void setConfig(DataObject $config)
 */
class Category extends Note
{
    const DEFAULT_CATEGORY_LABEL = 'Not Selected';
    const BUTTON_OPEN_LABEL = 'Select Category...';
    const BUTTON_CLOSE_LABEL = 'Close';

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @var WidgetChooser
     */
    protected $widgetChooser;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param WidgetChooser $widgetChooser
     * @param CategoryFactory $categoryFactory
     * @param EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        WidgetChooser $widgetChooser,
        CategoryFactory $categoryFactory,
        EncoderInterface $jsonEncoder,
        $data = []
    ) {
        $this->widgetChooser = $widgetChooser;
        $this->categoryFactory = $categoryFactory;
        $this->jsonEncoder = $jsonEncoder;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    public function getElementHtml()
    {
        $this->prepareElementHtml($this);

        $html = $this->getBeforeElementHtml()
            . '<div id="' . $this->getHtmlId() . '" class="control-value admin__field-value" style="margin-right:20px;">' . $this->getText() . '</div>'
            . $this->getAfterElementHtml()
            . $this->getAfterElementJs();

        return $html;
    }

    protected function prepareElementHtml(AbstractElement $element)
    {
        $id = $this->getId();
        $config = $this->getConfig();

        // Chooser element
        $currentValue = $element->getValue();
        $currentLabel = $config->getLabel();
        $element->setId($id . 'label')->setForm($element->getForm());
        if ($currentValue) {
            $value = explode('/', $currentValue);
            $categoryId = false;

            if (isset($value[0]) && isset($value[1]) && $value[0] == 'category') {
                $categoryId = $value[1];
            }

            if ($categoryId) {
                $currentLabel = $this->categoryFactory->create()->load($categoryId)->getName();
            }
        }
        $element->setText($currentLabel);

        // Hidden element
        $hidden = $this->_factoryElement->create(Hidden::class, ['data' => $element->getData()]);
        $hidden->setId($id . 'value')->setForm($element->getForm())->setValue($currentValue);
        $hiddenHtml = $hidden->getElementHtml();

        // Button
        $buttons = $config->getButtons();
        $button = $this->widgetChooser->getLayout()->createBlock(
            Button::class
        )->setType(
            'button'
        )->setId(
            $id . 'control'
        )->setClass(
            'btn-chooser'
        )->setLabel(
            $buttons['open']
        )->setDisabled(
            $element->getReadonly()
        );
        $element->setData('after_element_html', $hiddenHtml . $button->toHtml());

        // Chooser Scripts
        $sourceUrl = $this->widgetChooser->getUrl(
            'catalog/category_widget/chooser',
            ['uniq_id' => $id, 'use_massaction' => false]
        );
        $configJson = $this->jsonEncoder->encode($config->getData());
        $afterElementJs = '<div id="' . $id . 'advice-container" class="hidden"></div>
            <script>
            require(["prototype", "mage/adminhtml/wysiwyg/widget"], function(){
            //<![CDATA[
                (function($) {
                    var instantiateChooser = function() {
                        window.' . $id . ' = new WysiwygWidget.chooser(
                            "' .  $id . '", 
                            "' .  $sourceUrl . '",
                             ' . $configJson . '
                        );
                        if ($("#' . $id . 'value")) {
                            $("#' . $id . 'value").advaiceContainer = "' . $id . 'advice-container";
                        }
                    }
                    
                    $(instantiateChooser);
                })(jQuery);
            //]]>
            });
            </script>
        ';
        $element->setData('after_element_js', $afterElementJs);
    }

    /**
     * Convert Array config to Object
     *
     * @return DataObject
     */
    protected function getConfig()
    {
        $config = new DataObject();
        $this->setConfig($config);

        // define chooser label
        $config->setData('label', self::DEFAULT_CATEGORY_LABEL);

        // chooser control buttons
        $buttons = [
            'open' => __(self::BUTTON_OPEN_LABEL),
            'close' => __(self::BUTTON_CLOSE_LABEL)
        ];

        $config->setButtons($buttons);

        return $config;
    }

}
