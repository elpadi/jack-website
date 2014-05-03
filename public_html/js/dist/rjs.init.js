require.config({
	baseUrl: '/project/js/dist',
	paths: {
		"site": "jack",
		"jquery": "vendor/jquery.min",
		"threejs": "vendor/three.min",
		"underscore": "vendor/underscore.min",
		"SectionSwitcher": "lib/SectionSwitcher.min"
	}
});
require(["main.min"]);
