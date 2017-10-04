(function($) {

	"use sctrict";

	var $fullScreenMedia = $('.full-screen-media');

	$fullScreenMedia.each(function(index, el) {

		var contentSize = $('.entry-content').width();
		var screenSize = $(window).width();
		var marginWidth = (screenSize / 2) - (contentSize/2);

		$(el).css({
			'margin-left': -marginWidth,
			'margin-right': -marginWidth
		});

		$fullScreenMedia.find('iframe').height(marginWidth);

	});



})(jQuery);