define([
    'jquery',
    'matchMedia',
    'jquery/ui'
], function($, mediaCheck) {
    'use strict';

    $.widget('mx.megaMenu', {
        _create: function() {
            this.init();
            this.bind();
        },

        init: function() {
            var self = this;

            if (!$('html').hasClass('mx-megamenu-init')) {
                $('html').addClass('mx-megamenu-init');

                this.element.data('mage-menu', 1); // Add mageMenu attribute to fix breadcrumbs rendering on product page

                $(document).on('click', '.action.nav-toggle', function () {
                    if ($('html').hasClass('nav-open')) {
                        $('html').removeClass('nav-open');

                        self.element.find('.level-top').removeClass('current');
                        self.element.find('.mx-megamenu__item').removeClass('current');

                        setTimeout(function () {
                            $('html').removeClass('nav-before-open');
                        }, 300);
                    } else {
                        $('html').addClass('nav-before-open');

                        setTimeout(function () {
                            $('html').addClass('nav-open');
                        }, 42);
                    }
                });
            }

            this._adjustSubMenuItems();

            this._fixFirstSubCategoryItem();

            // Add class for nav-anchor where the link has href
            this.element.find('.mx-megamenu__item .mx-megamenu__link').each(function(i, item) {
                if (self._hasSubmenu($(item))) {
                    $(item).addClass('has-submenu');
                }
            });
        },

        bind: function() {
            var self = this;

            mediaCheck({
                media: '(min-width: 1025px)',

                /**
                 * Switch to Desktop Version.
                 */
                entry: function () {
                    self.element.find('.level-top').hover(function() {
                        self.element.find('.level-top').removeClass('current');
                        $(this).addClass('current');
                    }, function() {
                        $(this).removeClass('current');
                    });

                    /**
                     * New functionality - toggle
                     */
                    self.element.find('.level1').find('.nav-anchor').each(function(i, el) {
                        if ($(el).hasClass('hide')) {
                            $(el).next('.mx-megamenu__submenu').hide();
                        }

                        if ($(el).hasClass('toggle')) {
                            $(el).next('.mx-megamenu__submenu').hide();
                            $(el).on('mouseenter', function() {
                                $(el).next('.mx-megamenu__submenu').show();
                            });

                            $(el).next('.mx-megamenu__submenu').on('mouseleave', function() {
                                $(this).hide();
                            });
                        }
                    });
                },
                /**
                 * Switch to Mobile Version.
                 */
                exit: function () {
                    var $item,
                        $items;

                    // Init sidebar links. Add link class for sidebar elements
                    self._initSideBarLinks();

                    self.element.find('.mx-megamenu__item > .mx-megamenu__link').on('click', function(e) {
                        $item = $(e.target).closest('.mx-megamenu__item');

                        if (self._canShowSubmenu($(e.target))) {
                            // Open, close
                            e.preventDefault();

                            if (!$item.hasClass('current')) {
                                if ($item.hasClass('level0')) {
                                    $items = $('.level0.mx-megamenu__item');
                                }

                                if ($item.hasClass('level1')) {
                                    $items = $('.level1.mx-megamenu__item');
                                }

                                if ($items.length) {
                                    $items.not($item).removeClass('current');
                                }
                            }

                            $item.toggleClass('current');
                        }
                    });
                }
            });
        },

        // Adjust sub menu items where wrapper is defined
        _adjustSubMenuItems: function() {
            var $parent,
                $subMenu;

            $('.mx-megamenu__submenu.wrapper').each(function(wrapperIndex, wrapperItem) {
                $subMenu = $(this);
                $parent = $subMenu.parent();

                $parent.find('.mx-megamenu__item').each(function(i, item) {
                  $subMenu.append($(item));
                });
            });
        },

        // Currently the first item on level1 is a duplicate of the parent category so it should be removed
        // TODO: fix the rendering
        _fixFirstSubCategoryItem: function() {
            $('.level0.mx-megamenu__submenu').find('.mx-megamenu__content').each(function(i, item) {
                $(this).find('.level1.mx-megamenu__item:first').remove();
            });
        },

        // Sidebar items with "menu-sidebar__item" class can be handled as generated links
        _initSideBarLinks: function() {
            this.element.find('.menu-sidebar__item').each(function(i, item) {
                $(item).addClass('mx-megamenu__item').addClass('level1');
                $(item).find('h4, a').addClass('mx-megamenu__link');
                $(item).find('ul').addClass('mx-megamenu__submenu');
            });
        },

        _canShowSubmenu: function($item) {
            var $link = $item.closest('.mx-megamenu__link');

            return this._hasSubmenu($link) || $link.next('.mx-megamenu__submenu').length
        },

        _hasSubmenu: function($item) {
            return $item.next('.mx-megamenu__submenu').length == 1;
        }
    });

    return $.mx.megaMenu;
});
