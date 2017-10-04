(($) ->

	class TomeDashboard

		constructor: ->
			if $('#tome-dashboard').length > 0
				@init();

		init: ->
			@initList();
			@list_content();


		list_content: ->
			$this = this;

			$('.action').click ->

				if $(this).hasClass('redirect')
					return;

				$(this).addClass('active').siblings('.active').removeClass('active');
				$('#chapters-widget').addClass('active');

				console.log( $(this).find('h2').text() );

				$.ajax
					url: ajaxurl
					method: "POST"
					data:
						action: "dashboard_list"
						post_type: $(this).data('type')
						new_link: $(this).data('new');
						heading_text: $(this).find('h2').text();

					success: (results) ->
						$('#chapters-widget').html( results )

						$this.initList()

						$('#chapters-widget').removeClass('active')


		initList: ->
			options = {
				valueNames: [ 'chapter-title' ]
				page: 10,
				plugins: [
					ListPagination({
						outerWindow: 1
					})
				]
			};

			chaptersList = new List('chapters-widget', options);


	dashboard = new TomeDashboard

) jQuery