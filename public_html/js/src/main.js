require(['jquery'], function(jquery) {
	if (document.body.className.indexOf('section-invite-confirmation') !== -1) {
		$(window).on('click', function(e) {
			if (['P','H1'].indexOf(e.target.nodeName) === -1) {
				window.location = $('#nda').data('forward-url');
			}
		});
	}
	if (document.body.className.indexOf('section-welcome') !== -1) {
		$(window).on('resize', function() {
			var $cont = $('#container');
			$cont.css('margin-top', $cont.outerHeight() < window.innerHeight ? ((window.innerHeight - $cont.outerHeight()) / 2) + 'px' : '');
		}).resize();
	}
});

var Jack = {
	Mag: {
		Section: {
			show: function($section) {
				$section.data('magazine-section').show();
			},
			face: function($section, face, immediate) {
				var section = $section.data('magazine-section');
				console.log('Jack.Mag.Section.face --- face:', face);
				if (typeof(face) !== 'string') return;
				((face === 'front') !== section.isShowingFront) && section.flip(immediate);
			},
			select: function($mag, $section) {
				$mag.data('magazine').currentSection = $section.data('magazine-section').getSectionIndex();
			},
			hide: function($section) {
				$section.data('magazine-section').hide();
			}
		},
		Thumbs: {
			select: function($mag, sectionName, isFront) {
				var $selected = $mag.find('.section-switcher__link').filter(function(i, el) { return el.getAttribute('href') === '#' + sectionName; });
				$mag.find('.section-switcher__links').children().removeClass('selected selected--front selected--back');
				$selected.closest('li').addClass('selected selected--' + (isFront ? 'front' : 'back'));
			}
		},
		Nav: {
			resetOpenToggler: function($mag, isOpen) {
				$mag.find('.magazine__toggle-open-button').data('togglebutton').setState(isOpen ? 'close' : 'open');
			},
			resetUrls: function($mag, mag, sectionName) {
				(sectionName !== 'centerfold') && $mag.find('.section-switcher__nav__next').attr('href', '#' + mag.getNextSection().name);
				(sectionName !== 'cover') && $mag.find('.section-switcher__nav__prev').attr('href', '#' + mag.getPreviousSection().name);
			},
			setEdgeClassnames: function($mag, sectionName) {
				$mag.find('.section-switcher__nav').toggleClass('left-edge', sectionName === 'cover').toggleClass('right-edge', sectionName === 'centerfold');
			}
		}
	}
};

require(['jquery','SectionSwitcher'], function(jquery, SectionSwitcher) {
	$('.magazine').on('sectionswitcher.switch', function(e) {
		/*
		var $mag = $(e.target);
		var mag = $mag.data('magazine');
		var section = mag.getCurrentSection();
		
		console.log('magazine.switchsection --- sectionName', section.name);
		Jack.Mag.Nav.resetOpenToggler($mag, section.isOpen);
		Jack.Mag.Nav.setEdgeClassnames($mag, mag, section.name);
		Jack.Mag.Nav.resetUrls($mag, mag, section.name);
		Jack.Mag.Thumbs.select($mag, section.name, section.isShowingFront);
		*/
	}).on('magazine.sectionflip', function(e) {
		var $mag = $(e.target);
		var section = $mag.data('magazine').getCurrentSection();
		Jack.Mag.Thumbs.select($mag, section.name, section.isShowingFront);
	}).sectionswitcher({
		currentIndex: 0,
		show: function($section, $container) {
		},
		hide: function($section, $container) {
		},
		transition: function($newSection, $oldSection, $container) {
			console.log('magazine.transition --- ');
			Jack.Mag.Section.hide($oldSection);
			$container.one('magazine.sectionhide', function() {
				Jack.Mag.Section.face($newSection, $($container.data('sectionswitcher').switchEvent.currentTarget).data('section-face'), true);
				Jack.Mag.Section.show($newSection);
				Jack.Mag.Section.select($container, $newSection);
				$container.trigger('sectionswitcher.switch')
			});
		},
	});
});

require(['jquery','threejs','site/MagazineManager'], function(jquery, threejs, MagazineManager) {
	var hasWebgl = window.WebGLRenderingContext && document.createElement('canvas').getContext('webgl');
	var aspectRatio = (window.CONFIG.MAGAZINE.WEBGL_WIDTH * 3) / (window.CONFIG.MAGAZINE.WEBGL_HEIGHT * 2);
	require(['site/Magazine/Webgl/' + (hasWebgl ? 'Shader' : 'Canvas') + 'Magazine'], function(Magazine) {
		$('.magazine').each(function(i, el) {
			$(el).data('magazine-manager', new MagazineManager($(el), Magazine));
		});
		$(window).on('resize', function() {
			$('.magazine').each(function(i, el) {
				var manager = $(el).data('magazine-manager');
				var $sections = manager.$container.find('.magazine__sections');
				var w = $sections.outerWidth(), h = $sections.outerHeight();
				if (aspectRatio > w / h) {
					w *= 0.75; h = w / aspectRatio;
				}
				else {
					h *= 0.75; w = h * aspectRatio;
				}
				manager._magazine.resize(w, h);
			});
		}).resize();
		require(['SectionSwitcher'], function(SectionSwitcher) {
			//$('.magazine').trigger('sectionswitcher.switch');
		});
	});
});

require(['jquery'], function(jquery) {
	$('.magazine__flip-button').on('click', function(e) {
		$(e.target).closest('.magazine').data('magazine-manager').flip();
	});
});

require(['jquery','lib/ui/ToggleButton'], function(jquery, ToggleButton) {
	$('.magazine__toggle-open-button').togglebutton().on('newstate', function(e) {
		console.log('toggle button . newstate', this);
		$(e.target).closest('.magazine').data('magazine-manager').openclose();
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
