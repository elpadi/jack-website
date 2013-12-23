require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"site": "../jack",
		"jquery": "jquery/jquery",
		"underscore": "underscore/underscore",
		"lib": "js-libs"
	}
});

require(['jquery'], function(jquery) {
	if (document.body.className.indexOf('section-invite-confirmation') !== -1) {
		$(window).on('click', function(e) {
			if (['P','H1'].indexOf(e.target.nodeName) === -1) {
				window.location = $('#nda').data('forward-url');
			}
		});
	}
});

require(['jquery','site/Magazine','lib/ui/SectionSwitcher/ArrowNav','site/SectionLinks','lib/ui/ToggleButton','lib/dom/trackers/resize'], function(jquery, Magazine, ArrowNav, SectionLinks, ToggleButton, onResize) {
	var $window = $(window);
	var mag = new Magazine($('.magazine'));
	if (mag.$container.length === 0) {
		return;
	}
	mag.addComponent(ArrowNav).addComponent(SectionLinks).init();
	window.mag = mag;

	var openClose = new ToggleButton($('#magazine__open-close-button'));
	openClose.on('newstate', function(state, oldState) {
		switch (oldState) {
			case 'open':
				return mag.openCurrentPoster();
			case 'close':
				return mag.closeCurrentPoster();
		}
	});
	$('#magazine__flip-button').on('click', function(e) {
		e.preventDefault();
		mag.flipCurrentPoster();
		return;
	});
	mag.on('sectionselected', function(newIndex, oldIndex, flipped) {
		openClose.setState(mag.$elements.eq(newIndex).hasClass('open') ? 'close' : 'open');
	});

	var resize = function(width, height, $page) {
		var isCenterfold = $page.hasClass('magazine-centerfold');
		var magHeight = Math.min(height * (76 / 100), isCenterfold ? height : $page.find('img').height());
		mag.$elementsContainer.css({
			height: magHeight + 'px',
			marginTop: Math.round((height - magHeight) / 2) + 'px'
		});
	}

	onResize(function(width, height) {
		resize(width, height, mag.getCurrentSection());
	});

	mag.on('beforeshow', function($el) {
		var width = $window.width();
		var height = $window.height();
		resize(width, height, $el);
	});
});

require(['lib/dom/absolute-fixed']);
require(['lib/dom/window-height']);
