(function () {
    tinymce.PluginManager.add('wdm_mce_button', function (editor, url) {
        editor.addButton('wdm_mce_button', {
            text: false,
            icon: 'icon dashicons-post-status',
            onclick: function () {
                // change the shortcode as per your requirement
                console.log(editor);
                editor.windowManager.open({
                    title: 'Insert ID Tag',
                    body: [
                        {
                            type: 'listbox',
                            name: 'level',
                            label: 'Header level',
                            'values': [
                                {text: '<h1>', value: '1'},
                                {text: '<h2>', value: '2'},
                                {text: '<h3>', value: '3'},
                                {text: '<h4>', value: '4'},
                                {text: '<h5>', value: '5'},
                                {text: '<h6>', value: '6'}
                            ]
                        },
                        {
                            type: 'textbox',
                            name: 'title',
                            label: 'Text'
                        },
                        {
                            type: 'textbox',
                            name: 'id',
                            label: 'ID'
                        },

                    ],
                    onsubmit: function( e ) {
                        editor.insertContent( '<h' + e.data.level + ' id="' + e.data.id + '">' + e.data.title + '</h' + e.data.level + '>');
                    }
                });
            }
        });
    });
})();
