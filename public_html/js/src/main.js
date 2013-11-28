require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"site": "../jack",
		"jquery": "jquery/jquery",
		"underscore": "underscore/underscore",
		"lib": "js-libs"
	}
});

require(['jquery','site/Magazine','site/ArrowNav','site/SectionLinks'], function(jquery, Magazine, ArrowNav, SectionLinks) {
	var mag = new Magazine($('.magazine'));
	mag.addComponent(ArrowNav).addComponent(SectionLinks).init();
	window.mag = mag;
});

require(['lib/dom/absolute-fixed']);
require(['lib/dom/window-height']);
