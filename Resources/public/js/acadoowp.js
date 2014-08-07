var app = app || {};

var windowWidth;

app.init = {
	bootstrap: function() {

		windowWidth = $(window).width();
		//run conditional init functions if selector exists on page
		for (var selector in app.init.selectors) {
			var $item = $(selector);
			if ($item.length) app.init.selectors[selector]($item);
		}

    },
	selectors: {

		'.google-map': function( $map_wrap ) {
			$map = $map_wrap.find('.map');
			app.mapping.init( $map );
		},

		'.search-toggle': function( $toggle ) {
			$toggle.on('click', function (e) {
				e.preventDefault();
				$('.main-search').toggle();
			});
		},

		'.accordion-item': function( $accordian ) {
			$accordian.on('click', 'a.accordion-toggle', function (e) {
				e.preventDefault();
				var $item = $(this).closest('.accordion-item');
				if ( ! $item.hasClass('open') ) {
					$('.accordion-item').removeClass('open');
					$item.addClass('open');
				} else {
					$('.accordion-item').removeClass('open');
				}
				scrollIntoView($item, 30);
			});
		},

		'.accordion.first-open': function( $accordian ) {
			$accordian.each(function () {
				$accordian.find(".accordion-item:first").addClass('open');
			});
		}
	}
};


app.mapping = {
	init: function( $el ) {

		// vars
		var latlng = new google.maps.LatLng( $el.attr('data-lat'), $el.attr('data-lng'));
		var args = {
			zoom     : parseInt($el.attr('data-zoom')),
			center   : latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			scrollwheel: false
		};

		// create map
		var map = new google.maps.Map( $el[0], args);

		// add marker
		if ( $el.attr('data-marker') && Boolean( $el.attr('data-marker') ) ) {
			var marker_args = {
				position	: latlng,
				map			: map
			};
			if ( $el.attr('data-marker-image') ) {
				marker_args.icon = $el.attr('data-marker-image');
			}
			var marker = new google.maps.Marker( marker_args );
		}

	}
}

app.init.bootstrap();


function scrollIntoView($item, offset, speed) {
	if (typeof offset == 'undefined') {
		offset = 0;
	}
	if (typeof speed == 'undefined') {
		speed = 500;
	}
	$('html, body').animate({
		scrollTop: $item.offset().top - offset
	}, speed);
}

