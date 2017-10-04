(($) ->
	class TomePublishBox
		
		constructor: ->
			@tomeSave()
			@tomeDelete()
			@showSaveButton()
			@cancelOption()
			@editOption()
			@togglePasswordField()

		tomeSave: ->
			$('.tome-publish').click ->
				$('#post').submit()

		tomeDelete: ->
			$('.tome-delete-link').click ->
				confirm('Are you sure, you want to delete this post?');


		showSaveButton: ->
			$('.custom-publish').find('input').change (data) ->
				$('.save-publish-options').slideDown()
				$('.tome-publish-actions').find('button, a').attr('disabled', '');

		cancelOption: ->
			$('.cancel-editing').click ->
				$('.options.active').removeClass('active').slideUp(250)
				$('.sub-wrapper').slideUp(250)
				$('.publish-cover').fadeOut(250);
				$('.tome-publish-actions').find('button, a').removeAttr('disabled');

		togglePasswordField: ->
			$('input[name="visibility"]').change ->
				if $(this).val() == 'password'
					$('#post_password').removeClass('hidden')
				else
					$('#post_password').addClass('hidden')

		editOption: ->
			$('.single-setting > .edit-link').click ->
				optionsID = $(this).data('options-id')

				if ( $('#'+optionsID).hasClass('active') == true )
					$('#'+optionsID).removeClass('active').slideUp(250);
					$('.sub-wrapper').slideUp(250)
					$('.publish-cover').fadeOut(250);
					$('.tome-publish-actions').find('button, a').removeAttr('disabled');
				else
					$('.sub-wrapper').find('.active').slideUp(250).removeClass('active')
					$('#'+optionsID).slideDown(250).addClass('active')
					$('.sub-wrapper').slideDown(250)
					$('.publish-cover').fadeIn(250);

	publishBox = new TomePublishBox
) jQuery
