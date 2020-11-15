$(document).ready(function() {
		$('.dropdown').each(function () {
		$(this).parent().eq(0).hoverIntent({
			timeout: 100,
			over: function () {
				var current = $('.dropdown:eq(0)', this);
				current.slideDown(100);
			},
			out: function () {
				var current = $('.dropdown:eq(0)', this);
				current.fadeOut(200);
			}
		});
	});
	
	/*$('.dropdown a').hover(function () {
		$(this).stop(true).animate({paddingLeft: '55px'}, {speed: 100, easing: 'easeOutBack'});
	}, function () {
		$(this).stop(true).animate({paddingLeft: '25px'}, {speed: 100, easing: 'easeOutBounce'});
	});*/
	
	
	
	});