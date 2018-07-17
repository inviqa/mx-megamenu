define([
    'jquery',
    'mage/template',
    'jquery/ui'
], function ($, mageTemplate) {
    "use strict";

    var $megaMenuContainer,
        $structureContainer,
        megaMenuEditor = '#megamenu-editor',
        menuItemTemplate = '#menu-item-template';

    $.widget('mx.megaMenuFormBuilder', {
        _create: function() {
            $megaMenuContainer = $('#megamenu-editor');
            $structureContainer = $megaMenuContainer.find('.structure');
        },

        buildItem: function(item) {
            var self = this,
                miscData,
                itemId = item['menu_item_id'],
                $dataProvider;

            // Build main list element
            var tmpl = mageTemplate(menuItemTemplate),
                $parentElement;

            var newItem = tmpl({
                data: {
                    id: itemId,
                    label: item['name']
                }
            });

            if (item['menu_item_parent_id'] == 0) {
                // No parent
                $structureContainer.append(newItem);
            } else {
                // Nested
                $parentElement = $('.dd-item-' + item['menu_item_parent_id']);
                if ($parentElement.length) {
                    if (!$parentElement.find('.dd-list').length) {
                        $parentElement.append('<ol class="dd-list" />');
                    }
                    $parentElement.find('.dd-list').append(newItem);
                }
            }

            // Build hidden values
            $.each(item, function(name, value) {
                self.saveHiddenElement(itemId, name, value);
            });

            // Build data for data provider
            miscData = {
                'name': 'category-label',
                'value': item['name']
            };
            $dataProvider = this.getDataProvider(itemId);
            $dataProvider.val(this.encodeParams(miscData));
        },

        saveHiddenElement: function(itemId, name, value) {
            var $megaMenuContainer = this.getMegaMenuContainer(itemId),
                elementClassName = this.getHiddenElementClassName(itemId, name),
                elementValue = this.getHiddenElementValue(name, value),
                $element = $megaMenuContainer.find('>.form').find('.' + elementClassName);

            if ($element.length) {
                $element.val(elementValue);
            } else {
                this.createHiddenElement(itemId, name, value);
            }
        },

        createHiddenElement: function(itemId, name, value) {
            var $megaMenuContainer = this.getMegaMenuContainer(itemId),
                elementClassName = this.getHiddenElementClassName(itemId, name),
                elementName = this.getHiddenElementName(itemId),
                elementValue = this.getHiddenElementValue(name, value);

            $megaMenuContainer.find('>.form').append("<input type='hidden' class='" + elementClassName + " menu_item_hidden' name='" + elementName + "' value='" + elementValue + "'/>");
        },

        getHiddenElementValue: function(name, value) {
            var params = {
                'name': name,
                'value': value
            };

            return this.encodeParams(params);
        },

        getHiddenElementName: function(itemId) {
            return 'menu_item_' + itemId;
        },

        getHiddenElementClassName: function(itemId, name) {
            return 'menu_item_' + itemId + '_' + name;
        },

        getDataProvider: function(itemId) {
            return this.getMegaMenuContainer(itemId).find('>.form').find('.data-provider');
        },

        getMegaMenuContainer: function(itemId) {
            return $(megaMenuEditor).find('.dd-item-' + itemId);
        },

        encodeParams: function(params) {
            if (params !== '') {
                return JSON.stringify(params);
            }

            return '';
        },

        decodeParams: function(params) {
            if (params !== '') {
                return JSON.parse(params);
            }

            return '';
        },

        encodeContent: function(name, value) {
            if (name.match('_content')) {
                value = this._escapeContent(value);

                return window.btoa(value);
            }

            return value;
        },

        decodeContent: function(name, value) {
            if (name.match('_content')) {
                value = window.atob(value);
                value = this._decodeSpecialCharacters(value);

                return this._escapeContent(value);
            }

            return value;
        },

        /**
         * Decode special symbols e.g. &reg; &copyright;
         * @param string content
         * @returns {*}
         * @private
         */
        _decodeSpecialCharacters: function(content) {
            return content.replace(/{amp}/g, '&').replace(/{comma}/g, ';');
        },

        /**
         * Escape content - workaround for html contents in widget textarea fields
         *
         * @param string content
         * @returns string
         * @private
         */
        _escapeContent: function(content) {
            return content.replace(/&gt;\s+&lt;/g,'&gt;&lt;').replace(/\"g/, '&quot;').replace(/&quot;/g, "'");
        }
    });

    return $.mx.megaMenuFormBuilder;
});
