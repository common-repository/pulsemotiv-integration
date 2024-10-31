( function( wp ) {
    var registerPlugin = wp.plugins.registerPlugin;
    var PluginSidebar = wp.editPost.PluginSidebar;
    var el = wp.element.createElement;
    var Text = wp.components.TextControl;

    registerPlugin( 'my-plugin-sidebar', {
        render: function() {
            return el( PluginSidebar,
                {
                    name: 'new-sidebar',
                    icon: 'admin-post',
                    title: 'My plugin sidebar',
                },
                el( 'div',
                    { className: 'plugin-sidebar-content' },
                    el( Text, {
                        label: 'Meta Block Field',
                        value: 'Initial value',
                        onChange: function( content ) {
                            console.log( 'content changed to ', content );
                        },
                    } )
                )
            );
        }
    } );
} )( window.wp );