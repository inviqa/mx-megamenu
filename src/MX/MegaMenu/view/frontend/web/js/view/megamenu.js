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

            // Add class for nav-anchor where the link has href
            this.element.find('.mx-megamenu__item .mx-megamenu__link').each(function(i, item) {
                if (!self._hasNoLink($(item))) {
                    $(item).addClass('has-link');
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
                },
                /**
                 * Switch to Mobile Version.
                 */
                exit: function () {
                    var $item,
                        $items;

                    self.element.find('.mx-megamenu__item > .mx-megamenu__link').on('click', function(e) {
                        $item = $(e.target).closest('.mx-megamenu__item');

                        if (self._hasNoLink($(e.target).closest('.mx-megamenu__link'))) {
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

        _hasNoLink: function($item) {
            return $item.attr('href') === 'javascript:;';
        }
    });

    return $.mx.megaMenu;
});
