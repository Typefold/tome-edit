$ = jQuery

class TomeModal

	constructor: ( modalId, modalAction, actionCallback ) ->

		this.modalId = modalId
		this.modalAction = modalAction
		this.modalEl = $( '#' + modalId )
		this.closeElement = '.close-modal'
		this.actionCallback = false

		if ( actionCallback && typeof actionCallback == 'function' )
			this.actionCallback = actionCallback

		this.openModal( modalId, modalAction )

		this.init()


	init: ->
		$('body').on 'click', this.closeElement, this.closeModal



	openModal: ( modalId, modalAction ) ->

		this.addBackdrop()
		this.modalEl.addClass('active');
		this.tabs()

		return this.getModalContent( modalAction ) if modalAction

		this.modalEl.removeClass('loading');



	getModalContent: ->
		_this = this

		$.ajax
			url: ajaxurl
			method: "POST"
			data:
				action: this.modalAction

			success: ( results ) ->
				_this.modalEl.find('.main-content' ).html( results ).removeClass('loading')

				_this.actionCallback( results ) if _this.actionCallback



	goToTab: ( tabID ) ->
		this.modalEl.find('.modal-section-tab').removeClass('active');

		this.modalEl.find('.modal-section').addClass('hidden');

		this.modalEl.find('#'+tabID).addClass('active');

		this.modalEl.find('.modal-section[tab-id="'+tabID+'"]').removeClass('hidden');



	tabs: ->
		this.modalEl.on 'click', '.modal-section-tab', ( evt ) => 
			tabEl = $(evt.target);
			sectionId = tabEl.attr('id');

			this.modalEl.find('.modal-section-tab').removeClass('active');

			tabEl.addClass('active');

			this.modalEl.find('.modal-section').addClass('hidden');

			this.modalEl.find('.modal-section[tab-id="'+sectionId+'"]').removeClass('hidden');



	# modalId - (optional) specific id of an modal
	closeModal: ( modalId ) ->
		$('.media-modal-backdrop').remove()


		if ( modalId != 'undefined' && typeof modalId == 'string' )
			return $( '#' + modalId ).removeClass('active')


		$(this).parents('.tome-modal').removeClass('active')




	addBackdrop: ->
		if ( $('.media-modal-backdrop').length == 0 )
			$('body').append('<div class="media-modal-backdrop"></div>');



	@removeBackdrop: ->
		$('.media-modal-backdrop').remove()



	getOpenModals: ->
		return $('.tome-modal.active')





# /*=============================================
# =            Dom interaction            =
# =============================================*/
$('body').on 'click', '.open-modal', ->
	modalId = $(this).attr('data-modal-id')
	modalAction = $(this).attr('data-action')

	window.activeModal = new TomeModal( modalId, modalAction )
