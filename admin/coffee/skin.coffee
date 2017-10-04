(($) ->
	# Custom "publish/update" button text.
	# This function copies the text from original Wordpress publish/update button

	previewButton = $('#preview-action').clone()
	$('#preview-action').remove()

	previewButton.find('a').text('Preview')

	$('#publishing-action').prepend(previewButton)

	$('.submitdelete').text('').addClass('dashicons dashicons-trash')

	class TomeSkinElements

		constructor: ->
			@tomeTabs()
			@toggleTooltips()

		tomeTabs: ->
			$('.tabs-nav li').click ->
				$(this).siblings('.active').removeClass('active')
				$(this).addClass('active')

				sectionId = $(this).data("section-id");

				$('#'+sectionId).addClass('active').siblings('.active').removeClass('active');


		toggleTooltips: ->

			$(window).load ->
				new Tooltip
					target: $('div[aria-label="Reference"]')[0]
					openOn: 'always'
					content: 'Manage your references and bibliography'
					classes: 'tooltip-theme-arrows tooltip-hidden references-tooltip'
					position: 'top center'


			$('.tooltip-holder').each (index, el) ->
				new Tooltip
				  target: $(el)[0]
				  openOn: 'always'
				  content: $(this).data('tooltip-content')
				  classes: 'tooltip-theme-arrows tooltip-hidden'
				  position: $(this).data('tooltip-position')

			$('#toplevel_page_tome-help a').click (e) ->
				e.preventDefault()

				$(this).toggleClass('active');
				$('.tooltip').toggleClass('tooltip-hidden')




	classInit = new TomeSkinElements()

) jQuery