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
            if( $('.bottom-header').hasClass('open-nav') && !$(event.target).closest('.bottom-header').length && !$(event.target).closest('#hamburger').length ) {
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

        '#more-results': function( $button ) {
            $button.on('click', function(e) {
                e.preventDefault();
                $(this).addClass('loading');
                var href = $button.attr('href').split('?');
                var query = href[1];

                var $list = $("<ul class='search-results'>");

                $("#results").append($list.load( location.origin + searchAjaxUrl + "?" + query + ".search-results li", function() {
                    var $urlHolder = $(this).find('#more-results-url');
                    var $moreResults = $('#more-results');
                    var newUrl = $urlHolder.data('url');
                    $moreResults.attr('href', newUrl).removeClass('loading');
                    $urlHolder.remove();
                }));
            });
            $button.appear();
            $button.on('appear', function(e){
                $(this).trigger('click');
            });
        },

        '.search-facet': function ($facet) {
            $facet.on('change', 'input[type="checkbox"]', function(e) {
                var $selectedOption = $(this);
                var $facet = $selectedOption.parents('.search-facet');
                if($facet.hasClass('facet-exclusive')){
                    $facet.find('input[type="checkbox"]')
                        .not('[value="' + $selectedOption.attr('value') +'"]')
                        .prop("checked", false);

                }
                var $options = $('.search-facet li input[type="checkbox"]');
                var $queryArr = [];
                $options.each(function(){
                    var $option = $(this);
                    if($option.prop('checked')) {
                        var name = $option.attr('name');
                        var value = $option.attr('value');
                        var found = false;
                        for(var i in $queryArr){
                            var object = $queryArr[i];
                            if(object.name == name){
                                object.values.push(value);
                                found = true;
                                break;
                            }
                        }
                        if(!found) {
                            $queryArr.push({name: name, values: [value]});
                        }
                    }
                });
                var queryStrings = [];
                if($queryArr.length > 0){
                    $queryArr.push({name: "paged", values: [1]});
                    for(var i in $queryArr){
                        var queryObject = $queryArr[i];
                        var valueString = queryObject.values.join(",");
                        var queryString = queryObject.name + "=" + valueString;
                        queryStrings.push(queryString);
                    }
                }
                var url = location.origin + searchAjaxUrl + "?" + queryStrings.join('&');
                $('#results').load( url + " .search-results li", function() {
                    var $urlHolder = $(this).find('#more-results-url');
                    var $moreResults = $('#more-results');
                    var newUrl = $urlHolder.data('url');
                    $moreResults.attr('href', newUrl);
                    $urlHolder.remove();
                });
            })
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

        '.image-overlay' : function ($overlay) {

            $overlay.find(".accordion-item:first").addClass('open');

            var $images = $overlay.find('.post-item-image img');
            $images.each(function() {
                var $image = $(this);
                var $width = $image.css('width');
                $image.closest('.image-overlay .post-item-image').css('width', $width);
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
                        min:5,
                        max:6
                    }
                },
                auto:{
                    play:true,
                    items:1
                }
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



