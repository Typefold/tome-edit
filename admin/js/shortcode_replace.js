(function($){

	tinymce.create('tinymce.plugins.shortcodeReplace', {

		init : function(ed, url) {
			var t = this;

			t.url = url;
			
			//replace shortcode before editor content set

			ed.on('BeforeSetcontent', function(o){
			  o.content = t._do_spot(o.content);
			});
			
			//replace shortcode as its inserted into editor (which uses the exec command)
			ed.onExecCommand.add(function(ed, cmd) {
			    if (cmd ==='mceInsertContent'){
					tinyMCE.activeEditor.setContent( t._do_spot(tinyMCE.activeEditor.getContent()) );
				}
			});

			//replace the image back to shortcode on save
			ed.onPostProcess.add(function(ed, o) {
				if (o.get)
					o.content = t._get_spot(o.content);
			});
		},

		_do_spot : function(editorContent) {
			var output =  editorContent.replace(/\[tome_place([^\]]*)\]/g, function(a,b) {
				return '<img src="" class="short-placeholder place mceItem" title="tome_place'+tinymce.DOM.encode(b)+'" data-mce-resize="false" data-mce-placeholder="1" />';
			});

			output =  output.replace(/\[tome_media([^\]]*)\]/g, function(a,b) {
				return '<img src="" class="short-placeholder media mceItem" title="tome_media'+tinymce.DOM.encode(b)+'" data-mce-resize="false" data-mce-placeholder="1" />';
			});

			output =  output.replace(/\[tome_reference(.*? id=['"](\d+)['"].*?)\](?:(.+?)?\[\/tome_reference\])/g, function(full, all_atts, ref_id, shortcode_content) {
				return '<span class="short-placeholder mceItem tome-reference" data-shortcode="tome_reference" data-ref-id="'+ref_id+'" data-attributes="'+tinymce.DOM.encode(all_atts)+'" data-mce-resize="false" data-mce-placeholder="1">'+shortcode_content+'</span>'
			});

			output =  output.replace(/\[tome_map([^\]]*)\]/g, function(a,b) {
				return '<img src="" class="short-placeholder map-placeholder mceItem" title="tome_map'+tinymce.DOM.encode(b)+'" data-mce-resize="false" data-mce-placeholder="1" />';
			});

			output =  output.replace(/\[tome_embed([^\]]*)\]/g, function(a,b) {
				return '<p class="short-placeholder" title="tome_embed'+tinymce.DOM.encode(b)+'">https://vimeo.com/181165364</p>';
			});


			return output;
		},

		_get_spot : function(content) {

			function getAttr(s, n) {
				n = new RegExp(n + '=\"([^\"]+)\"', 'g').exec(s);
				return n ? tinymce.DOM.decode(n[1]) : '';
			};


			var content = content.replace(/(<img[^>]+>|<p[^>]+>)/g, function(a,im) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('short-placeholder') != -1 ) {
					return '<p>['+tinymce.trim(getAttr(im, 'title'))+']</p>';
				}

				return a;
			});

			content = content.replace( /(<span[^>]+>)(.*?)(<\/span>)/g, function(a,im,c) {
				var cls = getAttr(im, 'class');

				if ( cls.indexOf('short-placeholder') === -1 )
					return a;

				var attributes = getAttr(im, 'data-attributes');
				var shortcode = getAttr(im, 'data-shortcode');

				return '['+tinymce.trim(shortcode)+attributes+']'+c+'[/'+tinymce.trim(shortcode)+']';
			});

			return content;

		}

	});

	tinymce.PluginManager.add('shortcodeReplace', tinymce.plugins.shortcodeReplace);

})(jQuery)
