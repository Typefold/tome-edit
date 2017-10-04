/* global tinyMCE */
(function($){
	var media = wp.media, shortcode_string = 'external_media';

	wp.mce = wp.mce || {};
	wp.mce.external_media = {
		shortcode_data: {},
		template: wp.media.template( 'external-media' ),
		getContent: function() {
			var options = this.shortcode.attrs.named;
			options.innercontent = this.shortcode.content;
			return this.template(options);
		},
		View: { // before WP 4.2:
			template: wp.media.template( 'editor-boutique-banner' ),
			postID: $('#post_ID').val(),
			initialize: function( options ) {
				this.shortcode = options.shortcode;
				wp.mce.external_media.shortcode_data = this.shortcode;
			},
			getHtml: function() {
				var options = this.shortcode.attrs.named;
				options.innercontent = this.shortcode.content;
				return this.template(options);
			}
		},
		edit: function( data ) {
			var shortcode_data = wp.shortcode.next(shortcode_string, data);
			var values = shortcode_data.shortcode.attrs.named;
			values.innercontent = shortcode_data.shortcode.content;
			wp.mce.external_media.popupwindow(tinyMCE.activeEditor, values);
		},
		// this is called from our tinymce plugin, also can call from our "edit" function above
		// wp.mce.external_media.popupwindow(tinyMCE.activeEditor, "bird");
		popupwindow: function(editor, values, onsubmit_callback){
			values = values || [];
			if(typeof onsubmit_callback !== 'function'){
				onsubmit_callback = function( e ) {
					// Insert content when the window form is submitted (this also replaces during edit, handy!)
					var args = {
							tag: shortcode_string,
							type: 'single',
							attrs: {
								id: values.id,
								size: e.data.size,
							}
						};
					editor.selection.setContent( wp.shortcode.string( args ) );
				};
			}
			editor.windowManager.open( {
				title: 'Tome Gallery',
				body: [
					{
						type: 'listbox',
						name: 'size',
						label: 'Size',
						value: values.size,
						'values': [
							{ value: "full-column", text: "Full Column" },
							{ value: "half-column", text: "Half Column" },
							{ value: "full-screen-media", text: "Full Scren" },
						]
					},
				],
				onsubmit: onsubmit_callback
			} );
		}
	};
	wp.mce.views.register( shortcode_string, wp.mce.external_media );

}(jQuery));