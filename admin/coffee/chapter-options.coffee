(($) -> 

	class ChapterOptions
		constructor: ->
			@saveOptions()

		saveOptions: ->
			$('.save-chapter-options').click (event) ->
				window.activeModal.closeModal( 'chapter-options-modal' )
				$('#publish').click()
				window.saved_from_modal = true

	chapterOpt = new ChapterOptions

) jQuery