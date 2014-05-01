require(['jquery'], function(jquery) {
	if (document.body.className.indexOf('section-invite-confirmation') !== -1) {
		$(window).on('click', function(e) {
			if (['P','H1'].indexOf(e.target.nodeName) === -1) {
				window.location = $('#nda').data('forward-url');
			}
		});
	}
});

require(['jquery','SectionSwitcher'], function(jquery, SectionSwitcher) {
	$('.magazine').on('sectionswitcher.switch', function(e) {
		var $mag = $(e.target);
		var mag = $mag.data('magazine');
		var sectionName = mag.getCurrentSection().name;
		
		console.log('magazine.switchsection --- sectionName', sectionName);
		$mag.find('.magazine__toggle-open-button').text(mag.getCurrentSection().isOpen ? 'close' : 'open');
		$mag.find('.section-switcher__nav').toggleClass('left-edge', sectionName === 'cover').toggleClass('right-edge', sectionName === 'centerfold');
		(sectionName !== 'centerfold') && $mag.find('.section-switcher__nav__next').attr('href', '#' + mag.getNextSection().name);
		(sectionName !== 'cover') && $mag.find('.section-switcher__nav__prev').attr('href', '#' + mag.getPreviousSection().name);
	}).sectionswitcher({
		currentIndex: 0,
		show: function($section, $container) {
		},
		hide: function($section, $container) {
		},
		transition: function($newSection, $oldSection, $container) {
			var mag = $container.data('magazine');
			var section = $newSection.data('magazine-section');
			console.log('magazine.transition --- section', section);
			$oldSection.data('magazine-section').hide();
			$container.one('magazine.sectionhide', function() {
				section.show();
				mag.currentSection = section.getSectionIndex();
				console.log(mag.currentSection);
				$container.trigger('sectionswitcher.switch')
			});
		},
	});
});

require(['jquery','site/Magazine'], function(jquery, Magazine) {
	var init_magazine = function(Mag) {
		Magazine.jqueryPlugin(Mag);
		$('.magazine').magazine();
		require(['SectionSwitcher'], function(SectionSwitcher) {
			$('.magazine').trigger('sectionswitcher.switch');
		});
	};
	if (Modernizr.webgl) {
		require(['site/Magazine/Webgl/Magazine'], init_magazine);
	}
});

require(['jquery'], function(jquery) {
	$('.magazine__flip-button').on('click', function(e) {
		$(e.target).closest('.magazine').data('magazine').getCurrentSection().flip();
	});
});

require(['jquery','lib/ui/ToggleButton'], function(jquery, ToggleButton) {
	$('.magazine__toggle-open-button').togglebutton().on('newstate', function() {
		console.log('toggle button . newstate', this);
		$(this).closest('.magazine').data('magazine').getCurrentSection().toggleViewState();
	});
	/*
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
	*/
});

require(['lib/dom/absolute-fixed']);
require(['lib/dom/window-height']);
