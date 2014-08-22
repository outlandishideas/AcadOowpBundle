(function() {
	var defaultOptions = {
		selectors: {
			option: '.filter-option',
			selectedList: '#filter-selected ul',
			queryBox: '#q',
			querySubmit: '#submit-q',
			filterPanel: '#filter-panel',
			filterPanelToggle: '.filter-toggle',
			filterSection: '.filter-section',
			filterSectionTab: '#filter-tabs li',
			clearFilter: '.filter-clear'
		},
		autoSearch: true
	};

	var currentRotation = 0;

	var setupEvents = function(fs) {
		$(fs.options.selectors.option).click(function(e) {
			e.preventDefault();
			var t = $(e.target.nodeName == 'A' ? e.target.parentNode : e.target);
			var selector = t.attr('id') || t.attr('rel');
			if (fs.isSelected(selector)) {
				$('#'+selector).removeClass('selected');
				$('[rel='+selector+']').fadeOut(400, function() {this.remove();});
				fs.removeSelection(selector);
			} else {
				$('#'+selector).addClass('selected');
				var c = t.clone(true);
				c.attr('id', '');
				c.attr('rel', selector);
				$(fs.options.selectors.selectedList).append(c);
				c.fadeOut(0);
				c.fadeIn(400);
				fs.addSelection(selector);
			}
			if (fs.options.autoSearch) {
				fs.search();
			}
		});

		$(fs.options.selectors.querySubmit).click(function(e) {
			e.preventDefault();
			fs.setSearchTerm($(fs.options.selectors.queryBox).prop('value'));
			fs.search();
		});

		$(fs.options.selectors.queryBox).keypress(function(e) {
			if (e.which == 13) {
				e.preventDefault();
				$(fs.options.selectors.querySubmit).click();
			}
		});

		if (fs.options.selectors.filterPanelToggle != '') {
			$(fs.options.selectors.filterPanelToggle).click(function(e) {
				e.preventDefault();
				$(fs.options.selectors.filterPanel).slideToggle(400);
			});
		}

		$(fs.options.selectors.filterSectionTab).click(function(e) {
			e.preventDefault();
			var t = $(e.target.nodeName == 'A' ? e.target.parentNode : e.target);
			if (t.hasClass('active')) return;
			$(fs.options.selectors.filterSectionTab).removeClass('active');
			t.addClass('active');
			$(fs.options.selectors.filterSection).css('display', 'none');
			$('#'+t.attr('rel')).css('display', 'block');
		});

		$(fs.options.selectors.clearFilter).click(function(e) {
			e.preventDefault();
			$('.filter-icon span').css('transform', 'rotate(180deg)');
			setTimeout(function() {
				$('.filter-icon span').css('transform', 'rotate(0deg)');
			}, 600);
			$.each(fs.selections, function(i, selector) {
				$('#'+selector).removeClass('selected');
				$('[rel='+selector+']').fadeOut(400, function() {this.remove();});
			});
			fs.clearSelection();
			if (fs.options.autoSearch) {
				fs.search();
			}
		});
	};

	var FacetSearch = function(options) {
		this.options = $.extend({}, defaultOptions, options);
		this.selections = [];
		this.searchTerm = '';
		this.filterPanelShowing = false;

		setupEvents(this);

		var hash = window.location.hash.slice(1);
		var that = this;
		if (hash) {
			var data = {};
			var parts = hash.split('&');
			$.each(parts, function(i, part) {
				part = part.split('=');
				switch (part[0]) {
					case 'q':
						that.searchTerm = decodeURIComponent(part[1]);
						break;
					case 's':
						if (part[1]) {
							var selections = part[1].split(',');
							$.each(selections, function(j, sel) {
								that.selections.push(decodeURIComponent(sel));
							});
						}
						break;
				}
			});
			$(this.options.selectors.queryBox).prop('value', this.searchTerm);
			$.each(this.selections, function(i, selector) {
				var t = $('#'+selector);
				t.addClass('selected');
				var c = t.clone(true);
				c.attr('id', '');
				c.attr('rel', selector);
				$(that.options.selectors.selectedList).append(c);
			});
			this.search();
		}
	};

	FacetSearch.prototype.addSelection = function(selection) {
		this.selections.push(selection);
	};

	FacetSearch.prototype.removeSelection = function(selection) {
		var id = $.inArray(selection, this.selections);
		if (id > -1) {
			this.selections.splice(id, 1);
		}
	};

	FacetSearch.prototype.clearSelection = function() {
		this.selections = [];
	};

	FacetSearch.prototype.isSelected = function(selection) {
		return $.inArray(selection, this.selections) > -1;
	}

	FacetSearch.prototype.setSearchTerm = function(searchTerm) {
		this.searchTerm = searchTerm;
	};

	FacetSearch.prototype.search = function() {
		currentRotation += 720;
		$(this.options.selectors.querySubmit+' span').css('transform', 'rotate('+currentRotation+'deg)');
		var query = [];
		$.each(this.selections, function (i, selection) {
			var parts = selection.split('_');
			var s = parts[1].replace(/---/g, ')').replace(/--/g, '(').replace(/-/g, ' ');
			query.push(parts[0]+'[]='+encodeURIComponent(s));
		});
		if (this.searchTerm) {
			query.push('q='+encodeURIComponent(this.searchTerm));
		}
		var queryString = query.join('&');
		var hash = 's='+this.selections.map(function(s) {return encodeURIComponent(s);}).join(',')+'&q='+encodeURIComponent(this.searchTerm);
		$.ajax({
			url: window.searchAjaxUrl+'?'+queryString,
			success: function(data) {
				$('#content > div').html(data);
				window.location.hash = hash;
			}
		});
	};

	window['FacetSearch'] = FacetSearch;
})();