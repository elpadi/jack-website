var $ = require('jquery');

var App = require('./app');

var Intro = require('./pages/intro');
var ModelScroll = require('./pages/model');
var Models = require('./pages/models');

var LayoutSlideshow = require('./issues/layout');
var ImageGrid = require('./issues/image-grid');
var IssueSectionsInit = require('./issues/sections');

var Submenus = require('./layouts/submenus');

window._app = new App();
window._app.bootstrap();

$.fn.enableTapEvent = function() {
	var moved = false, obj = this;
	return this.on('touchstart', function(e) {
		if (e.originalEvent.touches.length === 1) setTimeout(function() {
			if (!moved) {
				clearTimeout(obj.data('tapClickTimeoutId'));
				obj.data('wasTapFired', true).trigger('tap', e);
				obj.data('tapClickTimeoutId', setTimeout(function() { obj.data('wasTapFired', false); }, 300));
			}
		}, 100);
	}).on('touchmove', function(e) {
		if (e.originalEvent.touches.length === 1) moved = true;
	}).on('touchend', function(e) {
		moved = false;
	});
};

$(document).ready(function() {

	window._app.addChild('submneus', new Submenus());

	if (location.pathname == '/') window._app.addChild('intro', new Intro());
	else document.body.classList.add('intro-finished');

	if (document.querySelector('section.models')) window._app.addChild('models', new Models());

	let ig = document.querySelector('.image-grid');
	if (ig) window._app.addChild('imageGrid', new ImageGrid(ig));

	if (window.innerWidth >= 768 && document.getElementById('issue-sections')) window._app.addChild('issue-sections', IssueSectionsInit);

	if (document.getElementById('layout-slideshow')) window._app.addChild('slideshow', new LayoutSlideshow());

	if (document.querySelectorAll('.models > .model').length == 1) window._app.addChild('modelscroll', new ModelScroll());

	window._app.dispatchEvent('init');

});

$(window).on('load', function() {

	window._app.dispatchEvent('load');

});

$(window).on('resize', function() {

	window._app.dispatchEvent('resize');

});
