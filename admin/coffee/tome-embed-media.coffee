initListPlugin = ( results ) ->
	options = {
		valueNames: ['media-title'],
		page: 9,
		# plugins: [
		# ListPagination({})
		# ]
	};

	mediaList = new List('embedded-media-list', options);

	window.mediaList = mediaList;