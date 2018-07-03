<?php

namespace MX\MegaMenu\Data\Form\Element;

use Magento\Framework\Data\Form\Element\Checkbox;

/**
 * Form toggle element
 */
class Toggle extends Checkbox
{
    public function getElementHtml()
    {
        $html = '';
        $htmlId = $this->getHtmlId();

        $beforeElementHtml = $this->getBeforeElementHtml();
        if ($beforeElementHtml) {
            $html .= '<label class="addbefore" for="' . $htmlId . '">' . $beforeElementHtml . '</label>';
        }

        $html .= '<div class="onoffswitch">';
        $html .= '<input id="' . $htmlId . '" name="' . $this->getName() . '" ' . $this->_getUiId() . ' value="' .
            $this->getEscapedValue() . '" ' . $this->serialize($this->getHtmlAttributes()) . ' class="onoffswitch-checkbox" />';
        $html .= '<label class="onoffswitch-label" for="' . $htmlId . '">' .
            '<span class="onoffswitch-inner"></span><span class="onoffswitch-switch"></span></label>';
        $html .= '</div>';

        $afterElementJs = $this->getAfterElementJs();
        if ($afterElementJs) {
            $html .= $afterElementJs;
        }

        $afterElementHtml = $this->getAfterElementHtml();
        if ($afterElementHtml) {
            $html .= '<label class="addafter" for="' . $htmlId . '">' . $afterElementHtml . '</label>';
        }

        return $html;
    }
}
