
var ExternalMediaForm = (function() {

	var ExternalMediaForm = function( $form, createCallback, deleteCallback, updateCallback ) {
		this.$form = $form;
		this.createCallback = createCallback;
		this.deleteCallback = deleteCallback;
		this.updateCallback = updateCallback;
		this.mediaID = false;
		this.init();
	}


	function addRegexValidation() {
		// regex youtube and vimeo url
		$.validator.addMethod(
			"video_regex",
			function(value, element, regexp) {
				var re = new RegExp(/https:\/\/(?:www.)?(?:vimeo.com\/(.*)|youtube.com\/watch\?v=(.*)?)/);
				return this.optional(element) || re.test(value);
			},
			"Must be a valid youtube or vimeo link."
		);

		$.validator.addMethod(
			"soundcloud_link",
			function(value, element, regexp) {
				var re = new RegExp(/^https?:\/\/(soundcloud\.com|snd\.sc)\/(.*)$/);
				return this.optional(element) || re.test(value);
			},
			"Must be a valid soundcloucd link"
		);
	}



	ExternalMediaForm.prototype.init = function() {
		this.formValidation();
		addRegexValidation();
		this.$form.submit( this.formSubmit.bind(this) );
	}


	
	ExternalMediaForm.prototype.setMediaID = function( mediaID ) {
		this.mediaID = mediaID;
	}



	/* Add new regex rules for #external_source field when #media_type field is changed */
	ExternalMediaForm.prototype.dynamicMediaTypeRules = function() {
		$media_type = this.$form.find('#media_type');
		$external_source = this.$form.find('#external_source');

		$media_type.change(function() {
			$external_source.rules("remove", "soundcloud_link");
			$external_source.rules("remove", "video_regex");

			switch ( $(this).val() ) {
				case 'video': $external_source.rules("add", "video_regex"); break;
				case 'audio': $external_source.rules("add", "soundcloud_link"); break;
				case 'embed':
				break;

			}
		});
	}



	ExternalMediaForm.prototype.formValidation = function() {

		this.$form.validate({
			rules: {
				media_type: {
					required: true
				},
				external_source: {
					required: true,
				},
				media_title: {
					required: true
				}
			}
		});

		this.dynamicMediaTypeRules();
	}



	ExternalMediaForm.prototype.createMedia = function( values ) {

		var args = {
			action: 'create_media',
			form_values: values
		}


		return $.post( ajaxurl, args )
	}



	ExternalMediaForm.prototype.deleteMedia = function( mediaID ) {
		var args = {
			action: 'delete_external_media',
			id: mediaID
		}

		return $.post( ajaxurl, args, this.deleteCallback );
	}



	ExternalMediaForm.prototype.populateForm = function( type, title, source ) {
		this.$form.find('#media_type').val( type );
		this.$form.find('#media_title').val( title );
		this.$form.find('#external_source').val( source );

		this.$form.find('#media_type').trigger('change');
	}



	ExternalMediaForm.prototype.formSubmit = function( event ) {
		event.preventDefault();

		if ( this.$form.valid() ) {

			var args = {
				media_type: this.$form.find('#media_type').val(),
				external_source: this.$form.find('#external_source').val(),
				media_title: this.$form.find('#media_title').val(),
				media_id: this.mediaID
			}

			var creatingMedia = this.createMedia(args);

			// this probably could be solved more elegantly
			// with event listeners
			if ( this.mediaID !== false ) {
				creatingMedia.then( this.updateCallback )
			} else {
				creatingMedia.then( this.createCallback )
			}

			creatingMedia.then( this.displayNotificationBox );


			$('.media-items').trigger('update');

		}
	}



	ExternalMediaForm.prototype.displayNotificationBox = function( response ) {

		$('.notification.active').removeClass('active');

		switch ( response ) {
			case '0':  $('.notification.alert').addClass('active'); break;
			default: $('.notification.success').addClass('active'); break;
		}

	}




	return ExternalMediaForm;
})();


var ExternalMediaPage = (function() {

	function ExternalMediaPage( mediaForm ) {
		this.form = mediaForm;
		this.init();
	}


	ExternalMediaPage.prototype.init = function() {
		this.bindUiInteraction();
		this.initList();
	}


	ExternalMediaPage.prototype.initList = function() {
		options = {
			valueNames: [ 'title' ],
		};
		chaptersList = new List('external-media-page', options);
	}


	/**
	 * TODO: what would be more elegent than globally function here?
	 * get data from media item element
	 * @param  {$mediaItem} $mediaItem - HTML element containing all inforamtion about the media
	 * @return {array}
	 */
	ExternalMediaPage.getMediaItemData = function( $mediaItem ) {
		return {
			type: $mediaItem.data('type'),
			title: $mediaItem.find('.title').text(),
			source: $mediaItem.find('.external-source').text(),
			mediaID: $mediaItem.data('item')
		};
	}



	ExternalMediaPage.prototype.bindUiInteraction = function() {
		self = this;

		$('.external-media-admin-page').on('click', '.delete-external-media', function() {
			var mediaID = $(this).parents('.media-item').data('item');

			popup = new ReferencesPopUp({
				message: "Are you sure?",
				onConfirm: function() {  self.form.deleteMedia( mediaID ) }
			});
		});


		$('.external-media-admin-page').on('click', '.edit-external-media', function() {
			var $mediaItem = $(this).parents('.media-item');
			var mediaInfo = ExternalMediaPage.getMediaItemData( $mediaItem );

			self.form.setMediaID( mediaInfo.mediaID );
			self.form.populateForm( mediaInfo.type, mediaInfo.title, mediaInfo.source );
			$('.media-form-wrapper').addClass('active');
		});


		$('.add-media').click(function(){
			$('.media-form-wrapper').toggleClass('active');
		});

	}


	return ExternalMediaPage;

})();




// External media page
(function($) {
	"use strict";

	if ( $('.external-media-admin-page').length == 0 )
		return false;

	var afterCreate = function( response ) {
		if ( response !== '0' )
			$('.media-items').prepend( response );
	}

	var afterDelete = function( response ) {
		$('.media-item[data-item="'+response+'"]').remove();
	}

	var afterUpdate = function( response ) {
		var mediaType = $(response).data('type');
		var mediaID = $(response).data('item');
		$('.media-item[data-item="'+mediaID+'"]').attr('data-type', mediaType).html( $(response).html() );
	}


	var mediaForm = new ExternalMediaForm( $('#external-media-form'), afterCreate, afterDelete, afterUpdate );
	var mediaPage = new ExternalMediaPage( mediaForm );



})(jQuery);




// Pages with #external-media-modal
(function($) {
	"use strict";

	if ( $('#embedd-media-modal').length == 0 )
		return false;


	var afterCreate = function( response ) {
		$('.media-items').prepend( response );
	}

	var afterDelete = function( response ) {
		$('.media-item[data-item="'+response+'"]').remove();
	}

	var afterUpdate = function( response ) {
		var mediaType = $(response).data('type');
		var mediaID = $(response).data('item');
		$('.media-item[data-item="'+mediaID+'"]').attr('data-type', mediaType).html( $(response).html() );
	}



	var mediaForm = new ExternalMediaForm( $('#external-media-form'), afterCreate, afterDelete, afterUpdate );



	/*============================================
	=            Modal UI interaction            =
	============================================*/
	$('#embedd-media-modal').on('click', '.edit-external-media', function() {
		var $mediaItem = $(this).parents('.media-item');
		var mediaInfo = ExternalMediaPage.getMediaItemData( $mediaItem );

		mediaForm.setMediaID( mediaInfo.mediaID );
		mediaForm.populateForm( mediaInfo.type, mediaInfo.title, mediaInfo.source );

		$('.media-form-wrapper').addClass('active');
	});


	$('#embedd-media-modal').on('click', '.delete-external-media', function() {
		var mediaID = $(this).parents('.media-item').data('item');

		popup = new ReferencesPopUp({
			message: "Are you sure?",
			onConfirm: function() {  mediaForm.deleteMedia( mediaID ) }
		});
	});


	$('#embedd-media-modal').on('click', '.item-info', function() {

	});

	$('.add-media').click(function(){
		$('.media-form-wrapper').toggleClass('active');
	});


	$('#embedd-media-modal').on('click', '.item-info', function() {
		var $mediaItem, mediaInfo, shortcodeString;

		$mediaItem = $(this).parents('.media-item');
		mediaInfo = ExternalMediaPage.getMediaItemData( $mediaItem );
		shortcodeString = '[external_media id="'+mediaInfo.mediaID+'" size="full-column"]';

		tinyMCE.activeEditor.insertContent( shortcodeString );
		window.activeModal.closeModal( 'embedd-media-modal' );
	});


})(jQuery);








