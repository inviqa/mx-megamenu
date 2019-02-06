define([
    'jquery',
    'mage/template',
    'MXMegaMenuFormBuilder',
    'MXMegaMenuFormDialog',
    'nestable',
    'mage/adminhtml/browser',
    'jquery/ui'
], function ($, mageTemplate, formBuilder, menuDialog) {
    "use strict";

    var $megaMenuContainer,
        $structureContainer,
        $actionsContainer,
        $tabsMenu,
        $saveButton,
        $settingsForm,
        $menuItemsForm,
        $nestableContainer,
        dataProviderLabel = 'data-provider',
        categoryNameLabel = 'category-label',
        classnameLabel = 'classname',
        formKeyLabel = 'form_key',
        defaultMenuItemLabel = 'Menu Item';

    $.widget('mx.megaMenuEditor', {
        options: {
            editUrl: '',
            saveUrl: '',
            maxDepth: 3
        },

        _create: function() {
            $megaMenuContainer = this.element;
            $structureContainer = $megaMenuContainer.find('.structure');
            $actionsContainer = $megaMenuContainer.find('.actions');
            $tabsMenu = $('#tabs-menu'),
            $saveButton = $('#save');
            $settingsForm = $('#settings_form');
            $menuItemsForm = $('#menu-items-form'),
            $nestableContainer = $menuItemsForm.find('.nestable');

            this.bind();
            this.build();
        },

        bind: function() {
            var self = this;

            $nestableContainer.nestable({
                maxDepth: self.options.maxDepth,
                onDragStart: function(l, e) {
                    $(e).find('.actions').hide();
                    $(e).find('.dd-actions').hide();
                },
                beforeDragStop: function(l, e) {
                    $(e).find('.actions').show();
                    $(e).find('.dd-actions').show();
                }
            });

            // Add
            $actionsContainer.find('.btn-add').on('click', function() {
                self.addMenuItem();
                $("html, body").animate({ scrollTop: $(document).height() }, 1000); // Scroll to the bottom as the new menu item is added to the bottom
            });

            //Tabs
            $tabsMenu.find('a').on('click', function(e) {
                var currentTab = $(e.target).data('target');

                $tabsMenu.find('li').removeClass('active');
                $(e.target).closest('li').addClass('active');

                $megaMenuContainer.find('.megamenu-tabs').removeClass('active');
                if ($(currentTab).length) {
                    $(currentTab).addClass('active');
                }
            });

            // Save
            $saveButton.on('click', function() {
                self.save();
            });
        },

        build: function() {
            if (menuItemsData) {
                // Build items
                this.buildItems(menuItemsData);

                // Add expand/collapse buttons as it's buggy for the third-party
                this.buildButtons();
            }
        },

        buildButtons: function() {
            var self = this;

            $structureContainer.find('.menu-item').each(function(i, el) {
                if ($(el).find('>.dd-list').length) {
                    $(el).find('>.dd-actions').append(self._getButtonHtml('#btn-expand-template'));
                    $(el).find('>.dd-actions').append(self._getButtonHtml('#btn-collapse-template'));
                    $(el).find('>.dd-actions').find('.btn-collapse').addClass('show');
                }
            });

            $structureContainer.find('.menu-item').find('.btn-caret').on('click', function(e) {
                e.preventDefault();

                var $this = $(e.target).closest('.btn-caret'),
                    $parent = $this.closest('.dd-actions'),
                    $expandButton = $parent.find('.btn-expand'),
                    $collapseButton = $parent.find('.btn-collapse'),
                    action = $this.data('action'),
                    $children = $this.closest('.menu-item').find('.dd-list');

                if (action === 'expand') {
                    $collapseButton.addClass('show');
                    $expandButton.removeClass('show');
                    $children.slideDown();
                }

                if (action === 'collapse') {
                    $expandButton.addClass('show');
                    $collapseButton.removeClass('show');
                    $children.slideUp();
                }
            });
        },

        save: function() {
            var self = this;

            if (this.options.saveUrl !== '') {
                var data = $settingsForm.serialize(),
                    itemsData = {};

                $structureContainer.find('.menu-item').each(function(i, el) {
                    var itemId = $(el).data('id'),
                        $form = $(el).find('>.form');

                    itemsData[itemId] = self._getDefaultsForMenuItem(itemId);

                    $form.find('input').each(function(idx, element) {
                        var item = formBuilder().decodeParams($(element).val()),
                            parentId = 0,
                            $parentElement;

                        // Get menu item data
                        if (!$(element).hasClass(dataProviderLabel) && self._canSaveMenuItem(item)) {
                            itemsData[itemId][item.name] = formBuilder().decodeContent(item.name, item.value);
                        }

                        // Get parent
                        $parentElement = $(element).closest('.dd-list').closest('.menu-item');
                        if ($parentElement.length && $parentElement.data('id') != itemId) {
                            parentId = $parentElement.data('id');
                        }
                        itemsData[itemId]['menu_item_parent_id'] = parentId;
                    });

                    // Get sort order
                    var sortOrder = $(el).index();
                    if (itemsData[itemId]['menu_item_parent_id'] != 0) {
                        sortOrder = (itemsData[itemId]['menu_item_parent_id'] * 100) + sortOrder;
                    }
                    itemsData[itemId]['sort_order'] = sortOrder;
                });

                if (itemsData !== '') {
                    data += '&items=' + encodeURIComponent(formBuilder().encodeParams(itemsData));
                }

                // Send Main Form Data
                $.ajax({
                    url: this.options.saveUrl,
                    type: 'POST',
                    data: data,
                    showLoader: true,
                    success: function(response) {
                        if (response.status && response.url) {
                            location.href = response.url;
                        }
                    }
                });
            }
        },

        buildItems: function(data) {
            var self = this,
                items = formBuilder().decodeParams(data);

            $.each(items, function(i, el) {
                self.addMenuItem(el);
            });
        },

        addMenuItem: function(item) {
            var self = this;

            if (typeof item === 'undefined') {
                var item = {
                    menu_item_id: this._getMaxMenuItemId() + 1,
                    menu_item_parent_id: 0,
                    name: formBuilder().encodeContent('name', defaultMenuItemLabel),
                    classname: 'new'
                };
            }

            formBuilder().buildItem(item);

            $(document).on('click', '.btn-edit', function(e) {
                e.stopImmediatePropagation();

                self.editMenuItem($(e.target).closest('.menu-item'));
            });

            $(document).on('click', '.btn-remove', function(e) {
                e.stopImmediatePropagation();

                self.removeMenuItem($(e.target).closest('.menu-item'));
            });
        },

        _getMaxMenuItemId: function() {
            var value,
                max = 0;

            $structureContainer.find('.menu-item').each(function() {
                value = parseInt($(this).data('id'));
                max = (value > max) ? value : max;
            });

            return max;
        },

        editMenuItem: function($element) {
            var additionalParams = '',
                $nameElement,
                menuItemNameValue,
                menuItemName = '';

            if ($element.data('id')) {
                additionalParams = 'item_id/' + $element.data('id');
                $nameElement = $element.find('.form').find('.menu_item_' + $element.data('id') + '_name');
                menuItemNameValue = $nameElement.val();
                menuItemNameValue = JSON.parse(menuItemNameValue);
                menuItemName = formBuilder().decodeContent('name', menuItemNameValue.value);
            }

            menuDialog().openDialog(this.options.editUrl + additionalParams, menuItemName);
        },

        removeMenuItem: function($element) {
            if ($element.length && confirm('Are you sure you want to remove this item?')) {
                $element.remove();
            }
        },

        _getButtonHtml: function(templateId) {
            var tmpl = mageTemplate(templateId);

            return tmpl();
        },

        _canSaveMenuItem(item) {
            return item.name !== categoryNameLabel
                && item.name != formKeyLabel
                && item.name !== classnameLabel;
        },

        _getDefaultsForMenuItem: function(itemId) {
            return {
                'menu_item_id': itemId,
                'status': 0,
                'name': defaultMenuItemLabel,
                'link': '',
                'custom_class': '',
                'header_status': 0,
                'header_content': '',
                'content_status': 0,
                'content_category': '',
                'content_content': '',
                'content_category_type': 'show',
                'content_type': 'wysiwyg',
                'remove_category_anchor': 0,
                'leftside_status': 0,
                'leftside_content': '',
                'rightside_status': 0,
                'rightside_content': '',
                'footer_status': 0,
                'footer_content': '',
                'sort_order': 0
            }
        }
    });

    return $.mx.megaMenuEditor;
});