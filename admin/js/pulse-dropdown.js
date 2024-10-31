(function() {
    tinymce.PluginManager.add('pulsemotiv_button', function( editor, url ) {
        var menuoptions = [];
        if (pulses) {
            pulses.map(function (pulse) {
                var option = {
                    text: pulse.name,
                    value: '[pulse id="' + pulse.id + '" uniqueid="' + pulse.uuid + '"]',
                    onclick: function () {
                        editor.insertContent(this.value());
                    }
                };
                menuoptions.push(option);
            });
            editor.addButton('pulsemotiv_button', {
                title: 'Select pulse to embed',
                type: 'menubutton',
                icon: 'icon pulse-icon',
                menu: menuoptions
            });
        }
    });
})();
