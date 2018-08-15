define([
    'jquery',
    'jquery/ui'
], function ($) {
    "use strict";

    // Widget for separate dialog instance to avoid the conflicts with other dialog instances opened by MediaBrowserUtility
    $.widget('mx.megaMenuDialog', {
        dialogId: 'mega-menu-dialog',
        title: 'Insert/Edit Form Data',

        _create: function() {
            this.getModalInstance();
        },

        /**
         * Get modal instance
         */
        getModalInstance: function() {
            var content = '<div class="popup-window magento-message" id="' + this.dialogId + '"></div>';

            if (menuModal) {
                menuModal.html($(content).html());
            } else {
                menuModal = $(content).modal($.extend({
                    title:  this.title,
                    modalClass: 'magento',
                    type: 'slide',
                    buttons: []
                }, []));
            }
        },

        openDialog: function(url, title) {
            if (typeof title !== 'undefined' && title !== '') {
                this.title += ' - ' + title;
            }

            menuModal.modal('setTitle', this.title);
            menuModal.modal('openModal');

            $.ajax({
                url: url,
                type: 'get',
                context: $(this),
                showLoader: true
            }).done(function (data) {
                menuModal.html(data).trigger('contentUpdated');
            });
        },

        /**
         * Close dialog.
         */
        closeDialog: function() {
            menuModal.modal('closeModal');
        }
    });

    return $.mx.megaMenuDialog;
});