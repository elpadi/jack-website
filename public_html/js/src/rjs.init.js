require.config({
	baseUrl: '/js/src/bower_components',
	paths: {
		"main": "../main",
		"main.admin": "../main.admin",
		"site": "../jack",
		"jquery": "jquery/dist/jquery",
		"threejs": "../vendor/three",
		"underscore": "underscore/underscore",
		"jquery-ui": "jquery-ui/ui/jquery-ui",
		"jui-accordion": "jquery-ui/ui/jquery.ui.accordion",
		"eventemitter": "eventemitter2/lib/eventemitter2",
		"dropzone": "dropzone/downloads/dropzone-amd-module",
		"SectionSwitcher": "SectionSwitcher/dist/SectionSwitcher",
		"lib": "js-libs"
	}
});
require([RJS_MAIN]);
