require.config({
	baseUrl: '/project/js/dist',
	paths: {
		"site": "jack",
		"jquery": "vendor/jquery",
		"threejs": "vendor/three",
		"underscore": "vendor/underscore",
		"SectionSwitcher": "vendor/SectionSwitcher"
	}
});
require(["main"]);

