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

        $(document).click(function(event) {
            if( $('.bottom-header').hasClass('open-nav') && !$(event.target).closest('.bottom-header').length  ) {
                toggleSideMenu();
            }
        });

        $(document).scroll(function() {

            if ($('.bottom-header').hasClass('open-nav') && $(window).height() > 430) {
                toggleSideMenu();
            }
        });

    },
	selectors: {

        '#hamburger': function($mobnav){
            $mobnav.on('click', function(event){
                event.preventDefault();
                toggleSideMenu();
            });
        },

		'.google-map': function( $map_wrap ) {
			$map = $map_wrap.find('.map');
			app.mapping.init( $map );
		},

		'.search-icon.toggle': function( $toggle ) {
			$toggle.on('click', function (e) {
				e.preventDefault();
				$('.main-search').slideToggle(300);
			});
		},

		'.accordion-item': function( $accordian ) {
			$accordian.on('click', 'a.accordion-toggle', function (e) {
				e.preventDefault();
				var $item = $(this).closest('.accordion-item');
				var open = $item.hasClass('open');
				$('.accordion-item').removeClass('open');
				if ( ! open ) {
					$item.addClass('open');
				}
				scrollIntoView($item, 30);
			});
		},

		'.accordion.first-open': function( $accordian ) {
			$accordian.each(function () {
				$accordian.find(".accordion-item:first").addClass('open');
			});
		},

        // Apply Carousel
        '.carousel': function($carousel) {

            $carousel.carouFredSel({
                circular: true,
                infinite: true,
                responsive: true,
                width: '100%',
                height: 'auto',
                items:{
                    visible:{
                        min:4,
                        max:5
                    }
                },
                auto:{
                    play:true,
                    items:1
                }
            });

        },
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

function isMobile() {
    return ($('.mobile-hide').is(":hidden"));
}

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

function toggleSideMenu() {
    $('.bottom-header').toggleClass('open-nav');
    $('body').toggleClass('push');
}

