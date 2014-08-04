require.config({
	baseUrl: '/project/js/dist',
	urlArgs: 'date=LAST_MTIME',
	paths: {
		"site": "jack",
		"jquery": "vendor/jquery",
		"threejs": "vendor/three",
		"underscore": "vendor/underscore",
		"jquery-ui": "vendor/jquery-ui",
		"jui-accordion": "vendor/jquery.ui.accordion",
		"eventemitter": "vendor/eventemitter2",
		"dropzone": "vendor/dropzone",
		"SectionSwitcher": "vendor/SectionSwitcher"
	}
});
require([RJS_MAIN]);

