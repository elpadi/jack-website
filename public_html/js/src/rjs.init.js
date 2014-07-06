require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"main": "../main",
		"site": "../jack",
		"jquery": "jquery/dist/jquery",
		"threejs": "../vendor/three",
		"underscore": "underscore/underscore",
		"SectionSwitcher": "SectionSwitcher/dist/SectionSwitcher",
		"lib": "js-libs"
	}
});
require(["main"]);