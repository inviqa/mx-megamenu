define([
    'jquery',
    'loader',
    'jquery/ui'
], function ($, loader) {
    "use strict";

    var $container,
    $exportButton;

    $.widget('mx.megaMenu', {
        options: {
            url: ''
        },

        _create: function() {
            $container = $('.megamenu-container');
            $exportButton = $container.find('.export');

            this.bind();
        },

        bind: function() {
            var self = this;

            $exportButton.on('click', function() {
                self.doExport();
            });
        },

        doExport: function() {
            var self = this;

            if (this.options.url !== '') {
                $('body').trigger('processStart');

                $.ajax({
                    url: this.options.url,
                    data: {
                        'form_key': $('input[name="form_key"]').val()
                    },
                    success: function(response) {
                        $('body').trigger('processStop');

                        if (response.status && response.result) {
                            self.saveFile(response.result);
                        }

                        if (response.redirect) {
                            location.href = response.redirect;
                        }
                    }
                });
            }
        },

        saveFile: function(content) {
            var blob = new Blob([content], { type: 'text/plain' }),
                anchor = document.createElement('a');

            anchor.download = "megamenu-export.json";
            anchor.href = (window.webkitURL || window.URL).createObjectURL(blob);
            anchor.dataset.downloadurl = ['application/json', anchor.download, anchor.href].join(':');
            anchor.click();
        }
    });

    return $.mx.megaMenu;
});