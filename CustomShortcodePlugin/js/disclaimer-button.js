(function() {
    tinymce.PluginManager.add('kr_csp_button', function( editor, url ) {
        editor.addButton( 'kr_csp_button', {
            title: 'Affiliate Disclaimer',
            text: 'Affiliate Disclaimer',
            type: 'menubutton',
            menu: [
                {
                    text: 'Affiliate Disclaimer',
                    value: '[affiliate]',
                    onclick: function() {
                        editor.insertContent(this.value());
                    }
                }
           ]
        });
    });
})();
