define([
    'jquery',
    'MXMegaMenuFormBuilder',
    'MXMegaMenuFormDialog',
    'jquery/ui'
], function ($, formBuilder, menuDialog) {
    "use strict";

    var defaultLabel = 'Menu Item',
        categoryLabelId = 'category-label',
        $generalNameContainer,
        $categoryLabelContainer;

    $.widget('mx.megaMenuForm', {
        options: {
            statusDisabled: 0,
            statusEnabled: 1
        },

        _create: function() {
            this.bind();
        },

        _init: function() {
            var $form = this._getForm();
            $generalNameContainer = $('#general_name');
            $categoryLabelContainer = $('#content_categorylabel');

            $form.find('.field-content_category').hide(); // Hide the category selector at content
            $form.find('.field-content_category_type').hide(); // Hide the category type selector at content

            // Load saved params
            this._loadSavedParams();
        },

        bind: function() {
            var self = this;

            // Save form
            this.element.find('.action-primary').on('click', function() {
                self._saveForm();
            });

            // Category / Content Switcher
            $('#content_chooser').on('change', function() {
                var value = $(this).val();

                self._switchContent(value);
            });

            // Category Select
            $('#content_categorycontrol').on('click', function() {
                if (typeof window.content_category !== 'undefined') {
                    window.content_category.choose();
                }
            });

            // Toggle - Status Box Change
            $('input[type="checkbox"]').on('change', function() {
                if ($(this).prop('checked')) {
                    $(this).val(self.options.statusEnabled);
                } else {
                    $(this).val(self.options.statusDisabled);
                }
            });
        },

        _loadSavedParams: function() {
            var self = this,
                itemId = this._getItemId(),
                $form = this._getForm(),
                $megaMenuContainer = formBuilder().getMegaMenuContainer(itemId),
                $dataProvider = formBuilder().getDataProvider(itemId),
                $elements = $megaMenuContainer.find('>.form').find('.menu_item_hidden'),
                $formElement,
                params,
                value;

            if ($elements.length) {
                $elements.each(function(i, el) {
                    params = formBuilder().decodeParams($(el).val());
                    $formElement = $form.find('[name="' + params.name + '"]');
                    if ($formElement.length) {
                        // Reset form element first
                        $formElement.val('');

                        value = params.value;

                        if ($formElement.is('select')) {
                            $formElement.val(value);
                            $formElement.trigger('change');
                        }

                        if ($formElement.is('input') || $formElement.is('textarea')) {
                            $formElement.val(formBuilder().decodeContent(params.name, value));
                        }

                        if ($formElement.hasClass('onoffswitch-checkbox')) {
                            $formElement.val(value);
                            if (params.value == self.options.statusEnabled) {
                                $formElement.prop('checked', true);
                            }
                        }
                    }
                });
            }

            if ($dataProvider.length) {
                // Load the category name back
                params = formBuilder().decodeParams($dataProvider.val());
                if (params.name === categoryLabelId) {
                    $categoryLabelContainer.html(params.value);
                }
            } else {
                // No params defined - set default label for name
                $generalNameContainer.val(defaultLabel);
            }
        },

        _saveForm: function() {
            var $form = this._getForm(),
                itemId = this._getItemId(),
                miscData,
                $dataProvider = formBuilder().getDataProvider(itemId),
                $megaMenuContainer = formBuilder().getMegaMenuContainer(itemId),
                name = defaultLabel,
                $formNameValue = $.trim($generalNameContainer.val());

            // Save form data
            $form.find('input,select,textarea').each(function(i, el) {
                var name = $(el).attr('name'),
                    value = $(el).val();

                formBuilder().saveHiddenElement(itemId, name, formBuilder().encodeContent(name, value));
            });

            // Save misc data - category name
            miscData = {
                'name': 'category-label',
                'value': $categoryLabelContainer.html()
            };
            $dataProvider.val(formBuilder().encodeParams(miscData));

            // Pass Category name for menu item
            if ($formNameValue !== '') {
                name = $formNameValue;
            }
            $megaMenuContainer.find('>.drag-menu').find('.label').html(name);

            menuDialog().closeDialog();
        },

        _switchContent: function(value) {
            var $form = this._getForm(),
                itemId = this._getItemId(),
                $element;

            $element = (value === 'category') ? $form.find('.field-content_' + value) : $form.find('.field-content_' + value + '_' + itemId);
            if ($element.length) {
                $form.find('.field-content_wysiwyg_' + itemId).hide();
                $form.find('.field-content_category').hide();
                $form.find('.field-content_category_type').hide();
                $element.show();

                if (value === 'category') {
                    $form.find('.field-content_category_type').show();
                }
            }
        },

        _getItemId: function() {
            var $form = this._getForm();

            return $form.find('input[name="menu_item_id"]').val();
        },

        _getForm: function() {
            return this.element.find('form');
        }
    });

    return $.mx.megaMenuForm;
});