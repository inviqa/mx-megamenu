define([
    'jquery',
    'loader',
    'jquery/ui'
], function ($, loader) {
    "use strict";

    var $container,
        $exportButton,
        $importButton,
        $form;

    $.widget('mx.megaMenu', {
        options: {
            url: ''
        },

        _create: function() {
            $container = $('.megamenu-container');
            $form = $container.find('.form');
            $exportButton = $container.find('.export');
            $importButton = $container.find('.import');

            this.bind();
        },

        bind: function() {
            var self = this;

            $exportButton.on('click', function() {
                self.doExport();
            });

            $importButton.on('click', function() {
                if (confirm('Are you sure? All your current data will be lost!')) {
                    self.doImport();
                }
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

        doImport: function() {
            if ($form.length && $form.find('.file').val()) {
                $form.submit();
            } else {
                alert('You need to specify the file to upload.');
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