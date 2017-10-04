(function($) {

	tinymce.PluginManager.add('tomeMediaSelector', function(editor, url) {

		if (editor.id !== "content")
			return;

		var selectorElement;

		$(document).mouseup(function(e) {
			var container = $("#tinymce, .media-selector");

			if (!container.is(e.target) // if the target of the click isn't the container...
				&&
				container.has(e.target).length === 0) // ... nor a descendant of the container
			{
				$('.media-selector').remove();
			}
		});



		$('body').on('mouseover', '.selector-action', function(evt, el) {

			tooltipText = $(this).data('tooltip');

			if ( tooltipText !== 'undefined' ) {
				$(this).html( '<span class="selector-tooltip">' + tooltipText + '</span>' )
			}
			
		});



		editor.on('keyup nodeChange', function() {

			if (emptyLine() === true) {
				mediaSelectorInit();
			} else {
				$('.media-selector').remove();
			}
		});

		/**
		 * Checks if a line, the cursor is on is empty
		 * @return {boolean}
		 */
		 function emptyLine() {
			var currentNode = editor.selection.getNode();
			return (editor.dom.isEmpty(currentNode) === true) ? true : false;
		 }

		/**
		 * Create media selector and place it on the right posiiton
		 * @return {void}
		 */
		 function mediaSelectorInit() {
			$('.media-selector').remove();

			mediaSelectorHtml();
			mediaSelectorPosition(selectorElement);
			commandsInit();

			$('body').append(selectorElement);
		 }

		/**
		 * Place tome media selector on the right place
		 * @param  {mediaSelector} - media selector HTML element
		 * @return {void}
		 */
		 function mediaSelectorPosition(mediaSelector) {

			var fromTop = editor.selection.getRng().startContainer.offsetTop,
				selection = editor.selection.win.getSelection(), // get the selection
				range = selection.getRangeAt(0), // the range at first selection group
				rect = range.getBoundingClientRect(), // and convert this to useful data
				iframePosition = $('#content_ifr').offset();

				$(mediaSelector).css({
					position: 'absolute',
					top: rect.top + iframePosition.top - 4 + fromTop + 'px',
				});

				$(mediaSelector).css('left', iframePosition.left - 20 + 'px');
			}

		/**
		 * Create media selector HTML element
		 * Assigns selectorElement property
		 * @return {void}
		 */
		 function mediaSelectorHtml() {

		 	var mediaSelector = document.createElement('div');

			// TO-DO
			// gallery and places modal window are ugly AF (thickbox class needed etc.)

			var selectorContent = '<span class="dashicons dashicons-plus"></span>';
			selectorContent += '<span id="media-selector-image" data-tooltip="media" class="selector-action dashicons dashicons-admin-media "></span>';
			selectorContent += '<span id="media-selector-embed" data-tooltip="external media" class="selector-action dashicons dashicons-editor-code open-modal" data-modal-id="embedd-media-modal"></span>';
			selectorContent += '<span id="media-selector-separator" data-tooltip="separator" class="selector-action dashicons dashicons-minus"></span>';
			selectorContent += '<a href="/wp-admin/admin-ajax.php?action=add_shortcode&type=tome_place&width=753&height=331" data-tooltip="places" id="media-selector-places" class="selector-action dashicons dashicons-location thickbox" data-editor="content"></a>';
			selectorContent += '<a href="/wp-admin/admin-ajax.php?action=add_shortcode&type=tome_gallery&width=753&height=198" data-tooltip="galleries" id="media-selector-gallery" class="selector-action thickbox dashicons dashicons-format-gallery" data-editor="content"></a>';
			selectorContent += '<span id="media-selector-map" data-tooltip="maps" class="selector-action open-modal" data-modal-id="tome-maps-modal"></span>';

			mediaSelector.className = "media-selector";

			$(mediaSelector).html(selectorContent);

			selectorElement = $(mediaSelector);

			return mediaSelector;
		}

		/**
		 * Open media selector when you click on "plus" icon
		 * @return {void}
		 */
		 function revealOptions() {
			$('body').on('click', '.dashicons-plus', function() {
				$(this).parent().toggleClass('active');
			});
		 }

		 function commandsInit() {
			$(selectorElement).on('click', '.selector-action', function(event) {

				// id of a command button
				var command = $(this).attr('id');

				switch (command) {
					case 'media-selector-image':
					openImageModal();
					break;
					case 'media-selector-separator':
					insertSeparator();
					break;
				}

			});
		 }

		 function openImageModal() {
			wp.media.editor.open();
		 }

		 function insertSeparator() {
			editor.execCommand('InsertHorizontalRule');
		 }

		 revealOptions();

		});
})(jQuery);










