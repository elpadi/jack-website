require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"site": "../jack",
		"jquery": "jquery/jquery",
		"underscore": "underscore/underscore",
		"lib": "js-libs"
	}
});

require(['jquery','site/Magazine','lib/ui/SectionSwitcher/ArrowNav','site/SectionLinks','lib/ui/ToggleButton'], function(jquery, Magazine, ArrowNav, SectionLinks, ToggleButton) {
	var mag = new Magazine($('.magazine'));
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
});

require(['lib/dom/absolute-fixed']);
require(['lib/dom/window-height']);
